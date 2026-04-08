<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\Handler;

use App\Entity\Order;
use App\Entity\StaticStorage\OrderStaticStorage;
use App\Form\Handler\OrderFormHandler;
use App\Message\SendOrderStatusChangedEmail;
use App\Utils\Manager\OrderManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderFormHandlerTest extends TestCase
{
    private MockObject&OrderManager $orderManager;
    private MockObject&EntityManagerInterface $entityManager;
    private MockObject&MessageBusInterface $bus;
    private MockObject&UnitOfWork $unitOfWork;
    private OrderFormHandler $handler;

    protected function setUp(): void
    {
        $this->orderManager = $this->createMock(OrderManager::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->bus = $this->createMock(MessageBusInterface::class);
        $this->unitOfWork = $this->createMock(UnitOfWork::class);

        $this->entityManager
            ->method('getUnitOfWork')
            ->willReturn($this->unitOfWork);

        $this->handler = new OrderFormHandler(
            $this->orderManager,
            $this->entityManager,
            $this->bus,
        );
    }

    public function testDispatchesMessageWhenStatusChanges(): void
    {
        $order = $this->createMock(Order::class);
        $order->method('getId')->willReturn(5);
        $order->method('getStatus')->willReturn(OrderStaticStorage::ORDER_STATUS_PROCESSED);

        $this->unitOfWork
            ->method('getOriginalEntityData')
            ->with($order)
            ->willReturn(['status' => OrderStaticStorage::ORDER_STATUS_CREATED]);

        $this->orderManager->expects($this->once())->method('save')->with($order);

        $this->bus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (SendOrderStatusChangedEmail $msg) {
                return $msg->getOrderId() === 5
                    && $msg->getNewStatus() === OrderStaticStorage::ORDER_STATUS_PROCESSED;
            }))
            ->willReturn(new Envelope(new SendOrderStatusChangedEmail(5, OrderStaticStorage::ORDER_STATUS_PROCESSED)));

        $result = $this->handler->processEditForm($order);

        $this->assertSame($order, $result);
    }

    public function testDoesNotDispatchWhenStatusUnchanged(): void
    {
        $order = $this->createMock(Order::class);
        $order->method('getStatus')->willReturn(OrderStaticStorage::ORDER_STATUS_CREATED);

        $this->unitOfWork
            ->method('getOriginalEntityData')
            ->with($order)
            ->willReturn(['status' => OrderStaticStorage::ORDER_STATUS_CREATED]);

        $this->orderManager->expects($this->once())->method('save');

        $this->bus->expects($this->never())->method('dispatch');

        $this->handler->processEditForm($order);
    }

    public function testDoesNotDispatchForNewOrder(): void
    {
        $order = $this->createMock(Order::class);
        $order->method('getStatus')->willReturn(OrderStaticStorage::ORDER_STATUS_CREATED);

        $this->unitOfWork
            ->method('getOriginalEntityData')
            ->with($order)
            ->willReturn([]);

        $this->orderManager->expects($this->once())->method('save');

        $this->bus->expects($this->never())->method('dispatch');

        $this->handler->processEditForm($order);
    }
}