<?php

namespace App\Tests\Unit\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use App\Entity\Cart;
use App\Entity\CartProduct;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    private Cart $cart;

    protected function setUp(): void
    {
        $this->cart = new Cart();
    }

    public function testNewCartHasDefaultValues(): void
    {
        $this->assertNotNull($this->cart->getCreatedAt());
        $this->assertEmpty($this->cart->getCartProducts());
    }

    public function testSetAndGetSessionId(): void
    {
        $sessionId = 'abc123xyz789';
        $this->cart->setSessionId($sessionId);
        
        $this->assertSame($sessionId, $this->cart->getSessionId());
    }

    public function testSetAndGetCreatedAt(): void
    {
        $date = new DateTimeImmutable('2024-01-01 12:00:00');
        $this->cart->setCreatedAt($date);
        
        $this->assertSame($date, $this->cart->getCreatedAt());
    }

    public function testAddCartProduct(): void
    {
        $cartProduct = new CartProduct();
        
        $this->cart->addCartProduct($cartProduct);
        
        $this->assertCount(1, $this->cart->getCartProducts());
        $this->assertTrue($this->cart->getCartProducts()->contains($cartProduct));
        $this->assertSame($this->cart, $cartProduct->getCart());
    }

    public function testAddSameCartProductTwiceDoesNotDuplicate(): void
    {
        $cartProduct = new CartProduct();
        
        $this->cart->addCartProduct($cartProduct);
        $this->cart->addCartProduct($cartProduct);
        
        $this->assertCount(1, $this->cart->getCartProducts());
    }

    public function testRemoveCartProduct(): void
    {
        $cartProduct = new CartProduct();
        $this->cart->addCartProduct($cartProduct);
        
        $this->cart->removeCartProduct($cartProduct);
        
        $this->assertCount(0, $this->cart->getCartProducts());
        $this->assertNull($cartProduct->getCart());
    }

    public function testCreatedAtIsSetOnConstruction(): void
    {
        $before = new DateTimeImmutable();
        $cart = new Cart();
        $after = new DateTimeImmutable();
        
        $this->assertInstanceOf(DateTimeInterface::class, $cart->getCreatedAt());
        $this->assertGreaterThanOrEqual($before, $cart->getCreatedAt());
        $this->assertLessThanOrEqual($after, $cart->getCreatedAt());
    }
}

