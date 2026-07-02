<?php

declare(strict_types=1);

namespace App\Tests\Unit\Message;

use App\Message\SendOrderConfirmationEmail;
use PHPUnit\Framework\TestCase;

class SendOrderConfirmationEmailTest extends TestCase
{
    public function testMessageStoresOrderId(): void
    {
        $message = new SendOrderConfirmationEmail(456);

        self::assertSame(456, $message->getOrderId());
    }

    public function testMessageWithDifferentOrderId(): void
    {
        $message = new SendOrderConfirmationEmail(999);

        self::assertSame(999, $message->getOrderId());
    }

    public function testMessageIsImmutable(): void
    {
        $message = new SendOrderConfirmationEmail(123);

        // Повторные вызовы возвращают то же значение
        self::assertSame(123, $message->getOrderId());
        self::assertSame(123, $message->getOrderId());
    }
}

