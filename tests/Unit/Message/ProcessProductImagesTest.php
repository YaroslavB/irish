<?php

declare(strict_types=1);

namespace App\Tests\Unit\Message;

use App\Message\ProcessProductImages;
use PHPUnit\Framework\TestCase;

class ProcessProductImagesTest extends TestCase
{
    public function testMessageStoresProductId(): void
    {
        $message = new ProcessProductImages(123);

        self::assertSame(123, $message->getProductId());
    }

    public function testMessageWithDefaultOperations(): void
    {
        $message = new ProcessProductImages(123);

        self::assertSame(['resize', 'optimize'], $message->getOperations());
    }

    public function testMessageWithCustomOperations(): void
    {
        $operations = ['resize', 'watermark'];
        $message = new ProcessProductImages(123, $operations);

        self::assertSame($operations, $message->getOperations());
    }

    public function testMessageWithEmptyOperations(): void
    {
        $message = new ProcessProductImages(123, []);

        self::assertSame([], $message->getOperations());
    }

    public function testMessageIsImmutable(): void
    {
        $message = new ProcessProductImages(123, ['resize']);

        // Повторные вызовы возвращают те же значения
        self::assertSame(123, $message->getProductId());
        self::assertSame(123, $message->getProductId());
        self::assertSame(['resize'], $message->getOperations());
        self::assertSame(['resize'], $message->getOperations());
    }
}

