<?php

declare(strict_types=1);

namespace App\Tests\Unit\MessageHandler;

use App\Entity\Order;
use App\Entity\StaticStorage\OrderStaticStorage;
use App\Entity\User;
use App\Message\SendOrderStatusChangedEmail;
use App\MessageHandler\SendOrderStatusChangedEmailHandler;
use App\Repository\OrderRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

class SendOrderStatusChangedEmailHandlerTest extends TestCase
{
    private MockObject&OrderRepository $orderRepository;
    private MockObject&MailerInterface $mailer;
    private SendOrderStatusChangedEmailHandler $handler;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->mailer = $this->createMock(MailerInterface::class);

        $this->handler = new SendOrderStatusChangedEmailHandler(
            $this->orderRepository,
            $this->mailer,
            'noreply@irish.local',
        );
    }

    public function testSendsEmailWithCorrectStatusLabel(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn('customer@example.com');
        $user->method('getFullName')->willReturn('Jane Doe');

        $order = $this->createMock(Order::class);
        $order->method('getId')->willReturn(7);
        $order->method('getOwner')->willReturn($user);
        $order->method('getOrderProducts')->willReturn(new \Doctrine\Common\Collections\ArrayCollection());
        $order->method('getTotalPrice')->willReturn(49.99);

        $this->orderRepository
            ->expects($this->once())
            ->method('find')
            ->with(7)
            ->willReturn($order);

        $this->mailer
            ->expects($this->once())
            ->method('send');

        ($this->handler)(new SendOrderStatusChangedEmail(7, OrderStaticStorage::ORDER_STATUS_PROCESSED));
    }

    public function testDoesNothingWhenOrderNotFound(): void
    {
        $this->orderRepository
            ->method('find')
            ->willReturn(null);

        $this->mailer
            ->expects($this->never())
            ->method('send');

        ($this->handler)(new SendOrderStatusChangedEmail(99, OrderStaticStorage::ORDER_STATUS_COMPLETED));
    }

    public function testDoesNothingWhenOwnerIsNull(): void
    {
        $order = $this->createMock(Order::class);
        $order->method('getOwner')->willReturn(null);

        $this->orderRepository
            ->method('find')
            ->willReturn($order);

        $this->mailer
            ->expects($this->never())
            ->method('send');

        ($this->handler)(new SendOrderStatusChangedEmail(1, OrderStaticStorage::ORDER_STATUS_CANCELED));
    }
}