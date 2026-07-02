<?php

declare(strict_types=1);

namespace App\Tests\Unit\MessageHandler;

use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Order;
use App\Entity\User;
use App\Message\SendOrderConfirmationEmail;
use App\MessageHandler\SendOrderConfirmationEmailHandler;
use App\Repository\OrderRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

class SendOrderConfirmationEmailHandlerTest extends TestCase
{
    private MockObject&OrderRepository $orderRepository;
    private MockObject&MailerInterface $mailer;
    private SendOrderConfirmationEmailHandler $handler;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->mailer = $this->createMock(MailerInterface::class);

        $this->handler = new SendOrderConfirmationEmailHandler(
            $this->orderRepository,
            $this->mailer,
            'noreply@irish.local',
        );
    }

    public function testSendsEmailForValidOrder(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn('customer@example.com');
        $user->method('getFullName')->willReturn('John Doe');

        $order = $this->createMock(Order::class);
        $order->method('getId')->willReturn(42);
        $order->method('getOwner')->willReturn($user);
        $order->method('getOrderProducts')->willReturn(new ArrayCollection());
        $order->method('getTotalPrice')->willReturn(99.99);

        $this->orderRepository
            ->expects($this->once())
            ->method('find')
            ->with(42)
            ->willReturn($order);

        $this->mailer
            ->expects($this->once())
            ->method('send');

        ($this->handler)(new SendOrderConfirmationEmail(42));
    }

    public function testDoesNothingWhenOrderNotFound(): void
    {
        $this->orderRepository
            ->method('find')
            ->willReturn(null);

        $this->mailer
            ->expects($this->never())
            ->method('send');

        ($this->handler)(new SendOrderConfirmationEmail(99));
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

        ($this->handler)(new SendOrderConfirmationEmail(1));
    }
}