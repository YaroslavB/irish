<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use App\Entity\Order;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testNewUserHasDefaultValues(): void
    {
        $this->assertFalse($this->user->isIsDeleted());
        $this->assertFalse($this->user->isVerified());
        $this->assertEmpty($this->user->getOrders());
    }

    public function testSetAndGetEmail(): void
    {
        $email = 'test@example.com';
        $this->user->setEmail($email);
        
        $this->assertSame($email, $this->user->getEmail());
        $this->assertSame($email, $this->user->getUserIdentifier());
    }

    public function testSetAndGetFullName(): void
    {
        $fullName = 'John Doe';
        $this->user->setFullName($fullName);
        
        $this->assertSame($fullName, $this->user->getFullName());
    }

    public function testSetAndGetPhone(): void
    {
        $phone = '+353 123 456 789';
        $this->user->setPhone($phone);
        
        $this->assertSame($phone, $this->user->getPhone());
    }

    public function testSetAndGetAddress(): void
    {
        $address = '123 Main Street, Dublin';
        $this->user->setAddress($address);
        
        $this->assertSame($address, $this->user->getAddress());
    }

    public function testSetAndGetZipcode(): void
    {
        $zipcode = '12345';
        $this->user->setZipcode($zipcode);
        
        $this->assertSame($zipcode, $this->user->getZipcode());
    }

    public function testSetAndGetPassword(): void
    {
        $password = 'hashed_password_123';
        $this->user->setPassword($password);
        
        $this->assertSame($password, $this->user->getPassword());
    }

    public function testDefaultRolesContainsRoleUser(): void
    {
        $roles = $this->user->getRoles();
        
        $this->assertContains('ROLE_USER', $roles);
    }

    public function testSetRolesAlwaysIncludesRoleUser(): void
    {
        $this->user->setRoles(['ROLE_ADMIN']);
        $roles = $this->user->getRoles();
        
        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles);
    }

    public function testSetAndGetIsVerified(): void
    {
        $this->assertFalse($this->user->isVerified());
        
        $this->user->setIsVerified(true);
        
        $this->assertTrue($this->user->isVerified());
    }

    public function testSetAndGetIsDeleted(): void
    {
        $this->assertFalse($this->user->isIsDeleted());

        $this->user->setIsDeleted(true);
        
        $this->assertTrue($this->user->isIsDeleted());
    }

    public function testAddAndRemoveOrder(): void
    {
        $order = new Order();
        
        $this->user->addOrder($order);
        $this->assertCount(1, $this->user->getOrders());
        $this->assertTrue($this->user->getOrders()->contains($order));
        
        $this->user->removeOrder($order);
        $this->assertCount(0, $this->user->getOrders());
    }

    public function testEraseCredentials(): void
    {
        // eraseCredentials should not throw an exception
        $this->user->eraseCredentials();
        $this->assertTrue(true);
    }
}
