<?php

namespace App\Utils\Manager;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\StaticStorage\OrderStaticStorage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class OrderManager extends AbstractManager
{
    private CartManager $cartManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        CartManager $cartManager
    ) {
        parent::__construct($entityManager);
        $this->cartManager = $cartManager;
    }

    public function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Order::class);
    }

    /**
     * @param object $order
     *
     * @return void
     */
    public function remove(object $order): void
    {
        $order->setIsDeleted(true);
        $this->save($order);
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
        if ($cart) {
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
            $orderProduct = new OrderProduct();
            $orderProduct->setAppOrder($order);
            $orderProduct->setQuantity($cartProduct->getQuantity());
            $orderProduct->setPricePerOne($cartProduct->getProduct()->getPrice());
            $orderProduct->setProduct($cartProduct->getProduct());

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
    }

    /**
     * @param object $entity
     */
    public function save(object $entity): void
    {
        $entity->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

}
