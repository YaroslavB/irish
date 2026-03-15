<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Category;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    private Category $category;

    protected function setUp(): void
    {
        $this->category = new Category();
    }

    public function testNewCategoryHasDefaultValues(): void
    {
        $this->assertFalse($this->category->isIsDeleted());
        $this->assertEmpty($this->category->getProducts());
    }

    public function testSetAndGetTitle(): void
    {
        $this->category->setTitle('electronics');
        
        // Title should be ucfirst(strtolower())
        $this->assertSame('Electronics', $this->category->getTitle());
    }

    public function testSetTitleNormalizesCase(): void
    {
        $this->category->setTitle('COMPUTERS');
        $this->assertSame('Computers', $this->category->getTitle());
        
        $this->category->setTitle('mObIlE pHoNeS');
        $this->assertSame('Mobile phones', $this->category->getTitle());
    }

    public function testSetAndGetSlug(): void
    {
        $slug = 'electronics';
        $this->category->setSlug($slug);
        
        $this->assertSame($slug, $this->category->getSlug());
    }

    public function testSetAndGetIsDeleted(): void
    {
        $this->assertFalse($this->category->isIsDeleted());

        $this->category->setIsDeleted(true);
        
        $this->assertTrue($this->category->isIsDeleted());
    }

    public function testAddAndRemoveProduct(): void
    {
        $product = new Product();
        
        $this->category->addProduct($product);
        $this->assertCount(1, $this->category->getProducts());
        $this->assertTrue($this->category->getProducts()->contains($product));
        
        $this->category->removeProduct($product);
        $this->assertCount(0, $this->category->getProducts());
    }

    public function testAddSameProductTwiceDoesNotDuplicate(): void
    {
        $product = new Product();
        
        $this->category->addProduct($product);
        $this->category->addProduct($product);
        
        $this->assertCount(1, $this->category->getProducts());
    }
}

