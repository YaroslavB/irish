<?php

declare(strict_types=1);

namespace App\Tests\Unit\Utils\Manager;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Exception\InsufficientStockException;
use App\Message\SendOrderConfirmationEmail;
use App\Utils\Manager\CartManager;
use App\Utils\Manager\OrderManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderManagerTest extends TestCase
{
    private OrderManager $orderManager;
    private MockObject&EntityManagerInterface $entityManager;
    private MockObject&CartManager $cartManager;
    private MockObject&ObjectRepository $repository;
    private MockObject&MessageBusInterface $bus;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->cartManager = $this->createMock(CartManager::class);
        $this->repository = $this->createMock(ObjectRepository::class);
        $this->bus = $this->createMock(MessageBusInterface::class);

        $this->entityManager
            ->method('getRepository')
            ->with(Order::class)
            ->willReturn($this->repository);

        $this->orderManager = new OrderManager($this->entityManager, $this->cartManager, $this->bus);
    }

    public function testGetRepositoryReturnsOrderRepository(): void
    {
        $this->assertSame($this->repository, $this->orderManager->getRepository());
    }

    public function testRemoveSetsIsDeletedAndSaves(): void
    {
        $order = $this->createMock(Order::class);
        $order->expects($this->once())->method('setIsDeleted')->with(true);
        $order->expects($this->once())
            ->method('setUpdatedAt')
            ->with($this->isInstanceOf(DateTimeImmutable::class));

        $this->entityManager->expects($this->once())->method('persist')->with($order);
        $this->entityManager->expects($this->once())->method('flush');

        $this->orderManager->remove($order);
    }

    public function testSaveSetsUpdatedAtAndPersists(): void
    {
        $order = $this->createMock(Order::class);
        $order->expects($this->once())
            ->method('setUpdatedAt')
            ->with($this->isInstanceOf(DateTimeImmutable::class));

        $this->entityManager->expects($this->once())->method('persist')->with($order);
        $this->entityManager->expects($this->once())->method('flush');

        $this->orderManager->save($order);
    }

    public function testSaveAcceptsNonOrderEntity(): void
    {
        $this->entityManager->expects($this->once())->method('persist');

        $this->orderManager->save(new stdClass());
    }

    public function testCreateOrderComputesExactTotalAndDecrementsStock(): void
    {
        // 3 × 0.10 + 1 × 0.20 is 0.5 exactly only without float math
        $first = $this->product('Stout', '0.10', 5);
        $second = $this->product('Cider', '0.20', 1);
        $cart = $this->cart([[$first, 3], [$second, 1]]);
        $this->givenPersistAssignsOrderId();

        $this->cartManager->expects($this->once())->method('remove')->with($cart);
        $this->bus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(SendOrderConfirmationEmail::class))
            ->willReturn(new Envelope(new SendOrderConfirmationEmail(1)));

        $order = $this->orderManager->createOrderFromCart($cart, new User());

        $this->assertSame('0.50', $order->getTotalPrice());
        $this->assertCount(2, $order->getOrderProducts());
        $this->assertSame(2, $first->getQuantity());
        $this->assertSame(0, $second->getQuantity());
    }

    public function testCreateOrderRejectsInsufficientStock(): void
    {
        $product = $this->product('Whiskey', '49.99', 1);
        $cart = $this->cart([[$product, 2]]);

        $this->entityManager->expects($this->never())->method('flush');
        $this->cartManager->expects($this->never())->method('remove');
        $this->bus->expects($this->never())->method('dispatch');

        $this->expectException(InsufficientStockException::class);
        $this->orderManager->createOrderFromCart($cart, new User());
    }

    /**
     * @dataProvider centsProvider
     */
    public function testToCents(string $price, int $expected): void
    {
        $this->assertSame($expected, OrderManager::toCents($price));
    }

    /**
     * @return array<string, array{string, int}>
     */
    public static function centsProvider(): array
    {
        return [
            'two decimals' => ['12.34', 1234],
            'one decimal'  => ['12.5', 1250],
            'integer'      => ['12', 1200],
            'zero'         => ['0.00', 0],
            'sub-unit'     => ['0.07', 7],
        ];
    }

    private function product(string $title, string $price, int $quantity): Product
    {
        return (new Product())->setTitle($title)->setPrice($price)->setQuantity($quantity);
    }

    /**
     * @param list<array{Product, int}> $lines
     */
    private function cart(array $lines): Cart
    {
        $cart = new Cart();
        foreach ($lines as [$product, $quantity]) {
            $cart->addCartProduct((new CartProduct())->setProduct($product)->setQuantity($quantity));
        }

        return $cart;
    }

    private function givenPersistAssignsOrderId(): void
    {
        $this->entityManager
            ->method('persist')
            ->willReturnCallback(static function (object $entity): void {
                if ($entity instanceof Order) {
                    (new ReflectionProperty(Order::class, 'id'))->setValue($entity, 1);
                }
            });
    }
}
