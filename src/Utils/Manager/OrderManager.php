<?php

namespace App\Utils\Manager;

use DateTimeImmutable;
use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\StaticStorage\OrderStaticStorage;
use App\Entity\User;
use App\Message\SendOrderConfirmationEmail;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderManager extends AbstractManager
{
    private CartManager $cartManager;
    private MessageBusInterface $bus;

    public function __construct(
        EntityManagerInterface $entityManager,
        CartManager $cartManager,
        MessageBusInterface $bus
    ) {
        parent::__construct($entityManager);
        $this->cartManager = $cartManager;
        $this->bus = $bus;
    }

    public function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Order::class);
    }

    public function remove(object $entity): void
    {
        /** @var Order $entity */
        $entity->setIsDeleted(true);
        $this->save($entity);
    }

    /**
     * @param string $sessionId
     * @param User   $user
     *
     * @return object|null
     */
    public function createOrderFromCartFromSession(
        string $sessionId,
        User $user
    ): ?object {
        $cart = $this->cartManager->getRepository()->findOneBy(
            ['sessionId' => $sessionId]
        );
        if ($cart instanceof Cart) {
            $this->createOrderFromCart($cart, $user);
        }

        return $cart;
    }


    private function createOrderFromCart(Cart $cart, User $user): void
    {
        $order = new Order();
        $order->setOwner($user);
        $order->setStatus(OrderStaticStorage::ORDER_STATUS_CREATED);
        $orderTotalPrice = 0.0;

        /** @var CartProduct $cartProduct */
        foreach ($cart->getCartProducts()->getValues() as $cartProduct) {
            $product = $cartProduct->getProduct();
            if ($product === null) {
                continue;
            }

            $orderProduct = new OrderProduct();
            $orderProduct->setAppOrder($order);
            $orderProduct->setQuantity((int) $cartProduct->getQuantity());
            $orderProduct->setPricePerOne((string) $product->getPrice());
            $orderProduct->setProduct($product);

            $quantity = (int) $orderProduct->getQuantity();
            $pricePerOne = (float) $orderProduct->getPricePerOne();
            $orderTotalPrice += $quantity * $pricePerOne;

            $order->addOrderProduct($orderProduct);
            $this->entityManager->persist($orderProduct);
        }

        $order->setTotalPrice($orderTotalPrice);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
        $this->cartManager->remove($cart);

        $this->bus->dispatch(new SendOrderConfirmationEmail((int) $order->getId()));
    }

    /**
     * @param object $entity
     */
    public function save(object $entity): void
    {
        $entity->setUpdatedAt(new DateTimeImmutable());
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

}
