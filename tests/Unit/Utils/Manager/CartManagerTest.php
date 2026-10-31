<?php

declare(strict_types=1);

namespace App\Tests\Unit\Utils\Manager;

use App\Entity\Cart;
use App\Entity\Product;
use App\Utils\Manager\CartManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class CartManagerTest extends TestCase
{
    private MockObject&EntityManagerInterface $entityManager;
    private MockObject&ObjectRepository $repository;
    private CartManager $cartManager;
    private Session $session;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(ObjectRepository::class);
        $this->entityManager->method('getRepository')->with(Cart::class)->willReturn($this->repository);
        $this->cartManager = new CartManager($this->entityManager);
        $this->session = new Session(new MockArraySessionStorage());
    }

    public function testFindSessionCartReturnsNullWithoutStoredId(): void
    {
        $this->repository->expects($this->never())->method('find');

        $this->assertNull($this->cartManager->findSessionCart($this->session));
    }

    public function testFindSessionCartLoadsStoredId(): void
    {
        $cart = new Cart();
        $this->session->set(CartManager::SESSION_KEY, 7);
        $this->repository->method('find')->with(7)->willReturn($cart);

        $this->assertSame($cart, $this->cartManager->findSessionCart($this->session));
    }

    public function testAddProductCreatesCartStoresIdAndIncrementsQuantity(): void
    {
        $product = new Product();
        $this->entityManager->method('persist')->willReturnCallback(static function (object $entity): void {
            if ($entity instanceof Cart) {
                (new ReflectionProperty(Cart::class, 'id'))->setValue($entity, 42);
            }
        });

        $cart = $this->cartManager->addProduct($this->session, $product);
        $this->assertSame(42, $this->session->get(CartManager::SESSION_KEY));
        $this->assertCount(1, $cart->getCartProducts());

        $this->repository->method('find')->with(42)->willReturn($cart);
        $cart = $this->cartManager->addProduct($this->session, $product);

        $this->assertCount(1, $cart->getCartProducts());
        $this->assertSame(2, $cart->getCartProducts()->first()->getQuantity());
    }
}
