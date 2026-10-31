<?php

declare(strict_types=1);

namespace App\Utils\Manager;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\StaticStorage\OrderStaticStorage;
use App\Entity\User;
use App\Exception\InsufficientStockException;
use App\Message\SendOrderConfirmationEmail;
use DateTimeImmutable;
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
     * Converts the cart into an order, decrements stock and removes the cart.
     *
     * @throws InsufficientStockException when any product has less stock than requested
     */
    public function createOrderFromCart(Cart $cart, User $user): Order
    {
        $order = new Order();
        $order->setOwner($user);
        $order->setStatus(OrderStaticStorage::ORDER_STATUS_CREATED);
        $totalCents = 0;

        // ponytail: no row lock, two concurrent checkouts can both pass the stock check; add PESSIMISTIC_WRITE when traffic warrants
        foreach ($cart->getCartProducts() as $cartProduct) {
            $product = $cartProduct->getProduct();
            if ($product === null) {
                continue;
            }

            $quantity = (int) $cartProduct->getQuantity();
            $inStock = (int) $product->getQuantity();
            if ($quantity > $inStock) {
                throw new InsufficientStockException(sprintf(
                    'Not enough stock for "%s": requested %d, available %d',
                    (string) $product->getTitle(),
                    $quantity,
                    $inStock
                ));
            }
            $product->setQuantity($inStock - $quantity);

            $pricePerOne = (string) $product->getPrice();
            $orderProduct = new OrderProduct();
            $orderProduct->setQuantity($quantity);
            $orderProduct->setPricePerOne($pricePerOne);
            $orderProduct->setProduct($product);
            $order->addOrderProduct($orderProduct);
            $this->entityManager->persist($orderProduct);

            $totalCents += $quantity * self::toCents($pricePerOne);
        }

        $order->setTotalPrice(sprintf('%d.%02d', intdiv($totalCents, 100), $totalCents % 100));
        $this->entityManager->persist($order);
        $this->entityManager->flush();
        $this->cartManager->remove($cart);

        $this->bus->dispatch(new SendOrderConfirmationEmail((int) $order->getId()));

        return $order;
    }

    public function save(object $entity): void
    {
        if ($entity instanceof Order) {
            $entity->setUpdatedAt(new DateTimeImmutable());
        }
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * "12.5" / "12.50" / "12" → 1250. Prices are decimal(6,2) strings, so no float math.
     */
    public static function toCents(string $price): int
    {
        [$whole, $fraction] = array_pad(explode('.', $price, 2), 2, '0');

        return (int) $whole * 100 + (int) str_pad(substr($fraction, 0, 2), 2, '0');
    }
}
