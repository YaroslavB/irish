<?php
declare(strict_types=1);
namespace App\Tests\Unit\Utils\Manager;
use App\Entity\Product;
use App\Utils\Manager\ProductImagesManager;
use App\Utils\Manager\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
class ProductManagerTest extends TestCase
{
    private ProductManager $productManager;
    private MockObject&EntityManagerInterface $entityManager;
    private MockObject&ProductImagesManager $imagesManager;
    private MockObject&ObjectRepository $repository;
    private string $productImagesDir = '/uploads/products';
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->imagesManager = $this->createMock(ProductImagesManager::class);
        $this->repository = $this->createMock(ObjectRepository::class);
        $this->entityManager
            ->method('getRepository')
            ->with(Product::class)
            ->willReturn($this->repository);
        $this->productManager = new ProductManager(
            $this->entityManager,
            $this->imagesManager,
            $this->productImagesDir
        );
    }
    public function testGetRepositoryReturnsProductRepository(): void
    {
        $result = $this->productManager->getRepository();
        $this->assertSame($this->repository, $result);
    }
    public function testGetProductImagesDirReturnsCorrectPath(): void
    {
        $product = $this->createMock(Product::class);
        $product->method('getId')->willReturn(42);
        $result = $this->productManager->getProductImagesDir($product);
        $this->assertEquals('/uploads/products/42', $result);
    }
    public function testRemoveSetsIsDeletedAndSaves(): void
    {
        $product = $this->createMock(Product::class);
        $product->expects($this->once())->method('setIsDeleted')->with(true);
        $this->entityManager->expects($this->once())->method('persist')->with($product);
        $this->entityManager->expects($this->once())->method('flush');
        $this->productManager->remove($product);
    }
    public function testUpdateProductImagesWithEmptyFileName(): void
    {
        $product = new Product();
        $product->setTitle('Test Product');
        $result = $this->productManager->updateProductImages($product, '');
        $this->assertSame($product, $result);
    }
}
