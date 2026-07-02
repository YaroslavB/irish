<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\ProcessProductImages;
use App\Repository\ProductRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

/**
 * Handler для асинхронной обработки изображений продукта.
 *
 * Поддерживаемые операции:
 * - resize: изменение размера изображений
 * - optimize: оптимизация размера файлов
 * - watermark: добавление водяного знака
 */
#[AsMessageHandler]
final class ProcessProductImagesHandler
{
    /**
     * @param ProductRepository $productRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly LoggerInterface $logger,
    ) {
    }


    /**
     * @param ProcessProductImages $message
     * @return void
     */
    public function __invoke(ProcessProductImages $message): void
    {
        $productId = $message->getProductId();
        $operations = $message->getOperations();

        $this->logger->info('Processing product images', [
            'product_id' => $productId,
            'operations' => $operations,
        ]);



        $product = $this->productRepository->find($productId);

        // Продукт не найден - не пытаемся повторить
        if ($product === null) {
            throw new UnrecoverableMessageHandlingException(
                sprintf('Product with ID %d not found', $productId)
            );
        }

        // Продукт удалён - не обрабатываем
        if ($product->getIsDeleted()) {
            $this->logger->info('Skipping deleted product', [
                'product_id' => $productId,
            ]);
            return;
        }

        $productImages = $product->getProductImages();

        if ($productImages->isEmpty()) {
            $this->logger->info('No images to process', [
                'product_id' => $productId,
            ]);
            return;
        }

        foreach ($operations as $operation) {
            $this->processOperation($productId, $operation);
        }

        $this->logger->info('Product images processed successfully', [
            'product_id' => $productId,
            'operations' => $operations,
            'images_count' => $productImages->count(),
        ]);
    }

    private function processOperation(int $productId, string $operation): void
    {
        match ($operation) {
            'resize' => $this->resizeImages($productId),
            'optimize' => $this->optimizeImages($productId),
            'watermark' => $this->addWatermark($productId),
            default => $this->logger->warning('Unknown operation', [
                'product_id' => $productId,
                'operation' => $operation,
            ]),
        };
    }

    private function resizeImages(int $productId): void
    {
        // Логика ресайза изображений
        $this->logger->debug('Resizing images', ['product_id' => $productId]);
    }

    private function optimizeImages(int $productId): void
    {
        // Логика оптимизации изображений
        $this->logger->debug('Optimizing images', ['product_id' => $productId]);
    }

    private function addWatermark(int $productId): void
    {
        // Логика добавления водяного знака
        $this->logger->debug('Adding watermark', ['product_id' => $productId]);
    }
}

