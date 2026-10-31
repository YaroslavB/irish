<?php

namespace App\Tests\Unit\Entity;

use DateTimeImmutable;
use App\Entity\Order;
use App\Entity\User;
use App\Entity\OrderProduct;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    private Order $order;

    protected function setUp(): void
    {
        $this->order = new Order();
    }

    public function testNewOrderHasDefaultValues(): void
    {
        $this->assertFalse($this->order->isDeleted());
        $this->assertNotNull($this->order->getCreatedAt());
        $this->assertNotNull($this->order->getUpdatedAt());
        $this->assertEmpty($this->order->getOrderProducts());
    }

    public function testSetAndGetOwner(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        
        $this->order->setOwner($user);
        
        $this->assertSame($user, $this->order->getOwner());
    }

    public function testSetAndGetStatus(): void
    {
        $status = 1;
        $this->order->setStatus($status);
        
        $this->assertSame($status, $this->order->getStatus());
    }

    public function testSetAndGetTotalPrice(): void
    {
        $totalPrice = '199.99';
        $this->order->setTotalPrice($totalPrice);
        
        $this->assertSame($totalPrice, $this->order->getTotalPrice());
    }

    public function testSetAndGetIsDeleted(): void
    {
        $this->assertFalse($this->order->isDeleted());
        
        $this->order->setIsDeleted(true);
        
        $this->assertTrue($this->order->isDeleted());
    }

    public function testCreatedAtIsSetOnConstruction(): void
    {
        $this->assertInstanceOf(DateTimeImmutable::class, $this->order->getCreatedAt());
    }

    public function testSetAndGetUpdatedAt(): void
    {
        $date = new DateTimeImmutable('2024-01-15 11:30:00');
        $this->order->setUpdatedAt($date);
        
        $this->assertSame($date, $this->order->getUpdatedAt());
    }

    public function testAddAndRemoveOrderProduct(): void
    {
        $orderProduct = new OrderProduct();
        
        $this->order->addOrderProduct($orderProduct);
        $this->assertCount(1, $this->order->getOrderProducts());
        $this->assertTrue($this->order->getOrderProducts()->contains($orderProduct));
        
        $this->order->removeOrderProduct($orderProduct);
        $this->assertCount(0, $this->order->getOrderProducts());
    }

    public function testCreatedAtAndUpdatedAtAreSetOnConstruction(): void
    {
        $before = new DateTimeImmutable();
        $order = new Order();
        $after = new DateTimeImmutable();
        
        $this->assertInstanceOf(DateTimeImmutable::class, $order->getCreatedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $order->getUpdatedAt());
        $this->assertGreaterThanOrEqual($before, $order->getCreatedAt());
        $this->assertLessThanOrEqual($after, $order->getCreatedAt());
        $this->assertGreaterThanOrEqual($before, $order->getUpdatedAt());
        $this->assertLessThanOrEqual($after, $order->getUpdatedAt());
    }
}

