<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller\Main;

use App\Controller\Main\CartController;
use App\Entity\Cart;
use App\Utils\Manager\CartManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class CartControllerTest extends TestCase
{
    public function testShowRendersCartFromSession(): void
    {
        $cart = new Cart();
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $cartManager = $this->createMock(CartManager::class);
        $cartManager->expects($this->once())
            ->method('findSessionCart')
            ->with($request->getSession())
            ->willReturn($cart);

        $controller = $this->getMockBuilder(CartController::class)->onlyMethods(['render'])->getMock();
        $controller->expects($this->once())
            ->method('render')
            ->with('main/cart/show.html.twig', ['cart' => $cart])
            ->willReturn(new Response());

        $controller->show($request, $cartManager);
    }

    public function testShowIgnoresForgedSessionCookie(): void
    {
        // A client-supplied PHPSESSID must not select a cart; only the server-side session does
        $request = new Request([], [], [], ['PHPSESSID' => 'victim_session']);
        $request->setSession(new Session(new MockArraySessionStorage()));

        $cartManager = $this->createMock(CartManager::class);
        $cartManager->method('findSessionCart')->willReturn(null);

        $controller = $this->getMockBuilder(CartController::class)->onlyMethods(['render'])->getMock();
        $controller->expects($this->once())
            ->method('render')
            ->with('main/cart/show.html.twig', ['cart' => null])
            ->willReturn(new Response());

        $controller->show($request, $cartManager);
    }
}
