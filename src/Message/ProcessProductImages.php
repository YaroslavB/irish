<?php

declare(strict_types=1);

namespace App\Message;

/**
 * Message для асинхронной обработки изображений продукта.
 *
 * Пример использования:
 * $this->messageBus->dispatch(new ProcessProductImages($productId, ['resize', 'watermark']));
 */
final class ProcessProductImages
{
    /**
     * @param int $productId ID продукта
     * @param string[] $operations Список операций: 'resize', 'watermark', 'optimize'
     */
    public function __construct(
        private readonly int $productId,
        private readonly array $operations = ['resize', 'optimize'],
    ) {
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return string[]
     */
    public function getOperations(): array
    {
        return $this->operations;
    }
}

