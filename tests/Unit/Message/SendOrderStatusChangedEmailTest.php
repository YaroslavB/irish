<?php

declare(strict_types=1);

namespace App\Tests\Unit\Message;

use App\Message\SendOrderStatusChangedEmail;
use PHPUnit\Framework\TestCase;

class SendOrderStatusChangedEmailTest extends TestCase
{
    public function testMessageStoresOrderId(): void
    {
        $message = new SendOrderStatusChangedEmail(123, 2);

        self::assertSame(123, $message->getOrderId());
    }

    public function testMessageStoresNewStatus(): void
    {
        $message = new SendOrderStatusChangedEmail(123, 3);

        self::assertSame(3, $message->getNewStatus());
    }

    public function testMessageWithDifferentValues(): void
    {
        $message = new SendOrderStatusChangedEmail(999, 5);

        self::assertSame(999, $message->getOrderId());
        self::assertSame(5, $message->getNewStatus());
    }

    public function testMessageIsImmutable(): void
    {
        $message = new SendOrderStatusChangedEmail(456, 2);

        // Повторные вызовы возвращают те же значения
        self::assertSame(456, $message->getOrderId());
        self::assertSame(456, $message->getOrderId());
        self::assertSame(2, $message->getNewStatus());
        self::assertSame(2, $message->getNewStatus());
    }
}

