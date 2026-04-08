<?php

declare(strict_types=1);

namespace App\Tests\Unit\Utils\Manager;

use App\Entity\Order;
use App\Message\SendOrderConfirmationEmail;
use App\Utils\Manager\CartManager;
use App\Utils\Manager\OrderManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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
        $result = $this->orderManager->getRepository();

        $this->assertSame($this->repository, $result);
    }

    public function testRemoveSetsIsDeletedAndSaves(): void
    {
        $order = $this->createMock(Order::class);

        $order->expects($this->once())
            ->method('setIsDeleted')
            ->with(true);

        $order->expects($this->once())
            ->method('setUpdatedAt')
            ->with($this->isInstanceOf(\DateTimeImmutable::class));

        $this->entityManager->expects($this->once())->method('persist')->with($order);
        $this->entityManager->expects($this->once())->method('flush');

        $this->orderManager->remove($order);
    }

    public function testCreateOrderFromCartFromSessionWithNoCart(): void
    {
        $cartRepository = $this->createMock(ObjectRepository::class);
        $this->cartManager
            ->method('getRepository')
            ->willReturn($cartRepository);

        $cartRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['sessionId' => 'session123'])
            ->willReturn(null);

        $user = $this->createMock(\App\Entity\User::class);
        $result = $this->orderManager->createOrderFromCartFromSession('session123', $user);

        $this->assertNull($result);
    }

    public function testSaveSetsUpdatedAtAndPersists(): void
    {
        $order = $this->createMock(Order::class);

        $order->expects($this->once())
            ->method('setUpdatedAt')
            ->with($this->isInstanceOf(\DateTimeImmutable::class));

        $this->entityManager->expects($this->once())->method('persist')->with($order);
        $this->entityManager->expects($this->once())->method('flush');

        $this->orderManager->save($order);
    }

    public function testCreateOrderFromCartFromSessionDispatchesConfirmationEmail(): void
    {
        $cartRepository = $this->createMock(ObjectRepository::class);
        $this->cartManager->method('getRepository')->willReturn($cartRepository);

        $cart = $this->createMock(\App\Entity\Cart::class);
        $cart->method('getCartProducts')->willReturn(new \Doctrine\Common\Collections\ArrayCollection());

        $cartRepository
            ->method('findOneBy')
            ->with(['sessionId' => 'abc'])
            ->willReturn($cart);

        $this->entityManager
            ->method('persist')
            ->willReturnCallback(function (object $entity): void {
                if ($entity instanceof Order) {
                    $reflection = new \ReflectionProperty(Order::class, 'id');
                    $reflection->setAccessible(true);
                    $reflection->setValue($entity, 1);
                }
            });
        $this->entityManager->method('flush');

        $this->bus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(SendOrderConfirmationEmail::class))
            ->willReturn(new Envelope(new SendOrderConfirmationEmail(0)));

        $user = $this->createMock(\App\Entity\User::class);
        $this->orderManager->createOrderFromCartFromSession('abc', $user);
    }
}

