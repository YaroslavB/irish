<?php

declare(strict_types=1);

namespace App\Tests\Unit\Utils\Manager;

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Utils\File\ImageResizer;
use App\Utils\Filesystem\FileSystemHelper;
use App\Utils\Manager\ProductImagesManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProductImagesManagerTest extends TestCase
{
    private ProductImagesManager $manager;
    private MockObject&EntityManagerInterface $entityManager;
    private MockObject&FileSystemHelper $fileSystem;
    private MockObject&ImageResizer $imageResizer;
    private string $uploadTempDir = '/tmp/uploads';

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->fileSystem = $this->createMock(FileSystemHelper::class);
        $this->imageResizer = $this->createMock(ImageResizer::class);

        $this->manager = new ProductImagesManager(
            $this->entityManager,
            $this->fileSystem,
            $this->imageResizer,
            $this->uploadTempDir
        );
    }

    public function testSaveImageForProductCreatesAllSizes(): void
    {
        $productDir = '/uploads/products/1';
        $tempFileName = 'temp_image.jpg';

        $this->fileSystem
            ->expects($this->once())
            ->method('createFolder')
            ->with($productDir);

        $this->imageResizer
            ->expects($this->exactly(3))
            ->method('resizeImageAndSave')
            ->willReturnOnConsecutiveCalls(
                'image_small.jpg',
                'image_middle.jpg',
                'image_big.jpg'
            );

        $result = $this->manager->saveImageForProduct($productDir, $tempFileName);

        $this->assertInstanceOf(ProductImage::class, $result);
        $this->assertEquals('image_small.jpg', $result->getFileNameSmall());
        $this->assertEquals('image_middle.jpg', $result->getFileNameMiddle());
        $this->assertEquals('image_big.jpg', $result->getFileNameBig());
    }

    public function testRemoveImageFromProductRemovesAllFiles(): void
    {
        $productDir = '/uploads/products/1';
        
        $product = $this->createMock(Product::class);
        $productImage = $this->createMock(ProductImage::class);
        
        $productImage->method('getFileNameSmall')->willReturn('small.jpg');
        $productImage->method('getFileNameMiddle')->willReturn('middle.jpg');
        $productImage->method('getFileNameBig')->willReturn('big.jpg');
        $productImage->method('getProduct')->willReturn($product);

        $this->fileSystem
            ->expects($this->exactly(3))
            ->method('remove');

        $product
            ->expects($this->once())
            ->method('removeProductImage')
            ->with($productImage);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->manager->removeImageFromProduct($productImage, $productDir);
    }

    public function testGetRepositoryReturnsProductImageRepository(): void
    {
        $repository = $this->createMock(ObjectRepository::class);
        
        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(ProductImage::class)
            ->willReturn($repository);

        $result = $this->manager->getRepository();

        $this->assertSame($repository, $result);
    }
}

