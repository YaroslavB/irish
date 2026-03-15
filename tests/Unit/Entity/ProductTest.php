<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\ProductImage;
use App\Entity\CartProduct;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private Product $product;

    protected function setUp(): void
    {
        $this->product = new Product();
    }

    public function testNewProductHasDefaultValues(): void
    {
        $this->assertFalse($this->product->isPublished());
        $this->assertFalse($this->product->isDeleted());
        $this->assertNotNull($this->product->getUuid());
        $this->assertNotNull($this->product->getCreatedAt());
        $this->assertEmpty($this->product->getProductImages());
        $this->assertEmpty($this->product->getCartProducts());
    }

    public function testSetAndGetTitle(): void
    {
        $title = 'Test Product';
        $this->product->setTitle($title);
        
        $this->assertSame($title, $this->product->getTitle());
    }

    public function testSetAndGetPrice(): void
    {
        $price = '99.99';
        $this->product->setPrice($price);
        
        $this->assertSame($price, $this->product->getPrice());
    }

    public function testSetAndGetQuantity(): void
    {
        $quantity = 100;
        $this->product->setQuantity($quantity);
        
        $this->assertSame($quantity, $this->product->getQuantity());
    }

    public function testSetAndGetDescription(): void
    {
        $description = 'This is a test product description.';
        $this->product->setDescription($description);
        
        $this->assertSame($description, $this->product->getDescription());
    }

    public function testSetAndGetIsPublished(): void
    {
        $this->assertFalse($this->product->getIsPublished());

        $this->product->setIsPublished(true);
        
        $this->assertTrue($this->product->getIsPublished());
    }

    public function testSetAndGetIsDeleted(): void
    {
        $this->assertFalse($this->product->getIsDeleted());

        $this->product->setIsDeleted(true);
        
        $this->assertTrue($this->product->getIsDeleted());
    }

    public function testSetAndGetCategory(): void
    {
        $category = new Category();
        $category->setTitle('Electronics');
        
        $this->product->setCategory($category);
        
        $this->assertSame($category, $this->product->getCategory());
    }

    public function testSetAndGetSlug(): void
    {
        $slug = 'test-product';
        $this->product->setSlug($slug);
        
        $this->assertSame($slug, $this->product->getSlug());
    }

    public function testAddAndRemoveProductImage(): void
    {
        $image = new ProductImage();
        
        $this->product->addProductImage($image);
        $this->assertCount(1, $this->product->getProductImages());
        $this->assertTrue($this->product->getProductImages()->contains($image));
        
        $this->product->removeProductImage($image);
        $this->assertCount(0, $this->product->getProductImages());
    }

    public function testAddAndRemoveCartProduct(): void
    {
        $cartProduct = new CartProduct();
        
        $this->product->addCartProduct($cartProduct);
        $this->assertCount(1, $this->product->getCartProducts());
        
        $this->product->removeCartProduct($cartProduct);
        $this->assertCount(0, $this->product->getCartProducts());
    }

    public function testUuidIsGeneratedOnConstruction(): void
    {
        $product1 = new Product();
        $product2 = new Product();
        
        $this->assertNotNull($product1->getUuid());
        $this->assertNotNull($product2->getUuid());
        $this->assertNotSame($product1->getUuid(), $product2->getUuid());
    }

    public function testCreatedAtIsSetOnConstruction(): void
    {
        $now = new \DateTimeImmutable();

        $this->assertInstanceOf(\DateTimeInterface::class, $this->product->getCreatedAt());
        $this->assertLessThanOrEqual($now, $this->product->getCreatedAt());
    }
}

