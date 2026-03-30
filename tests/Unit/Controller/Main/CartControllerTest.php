<?php
declare(strict_types=1);
namespace App\Tests\Unit\Controller\Main;
use App\Controller\Main\CartController;
use App\Entity\Cart;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Utils\Manager\OrderManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class CartControllerTest extends TestCase
{
    public function testShowReturnsCart(): void
    {
        $cart = new Cart();
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->method('findOneBy')
            ->with(['sessionId' => 'test_session'])
            ->willReturn($cart);
        $request = $this->createMock(Request::class);
        $request->cookies = new \Symfony\Component\HttpFoundation\ParameterBag(['PHPSESSID' => 'test_session']);
        $controller = $this->getMockBuilder(CartController::class)
            ->onlyMethods(['render'])
            ->getMock();
        $controller->expects($this->once())
            ->method('render')
            ->with('main/cart/show.html.twig', ['cart' => $cart])
            ->willReturn(new Response());
        $controller->show($request, $cartRepository);
    }
    public function testShowWithNoCart(): void
    {
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository->method('findOneBy')->willReturn(null);
        $request = $this->createMock(Request::class);
        $request->cookies = new \Symfony\Component\HttpFoundation\ParameterBag(['PHPSESSID' => 'no_cart']);
        $controller = $this->getMockBuilder(CartController::class)
            ->onlyMethods(['render'])
            ->getMock();
        $controller->expects($this->once())
            ->method('render')
            ->with('main/cart/show.html.twig', ['cart' => null])
            ->willReturn(new Response());
        $controller->show($request, $cartRepository);
    }
}
