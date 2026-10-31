<?php

declare(strict_types=1);

namespace App\Controller\Main;

use App\Entity\User;
use App\Exception\InsufficientStockException;
use App\Utils\Manager\CartManager;
use App\Utils\Manager\OrderManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'main_cart_show')]
    public function show(Request $request, CartManager $cartManager): Response
    {
        return $this->render('main/cart/show.html.twig', [
            'cart' => $cartManager->findSessionCart($request->getSession()),
        ]);
    }

    #[Route('/cart/create', name: 'main_cart_create')]
    public function create(
        Request $request,
        CartManager $cartManager,
        OrderManager $orderManager
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $session = $request->getSession();
        $cart = $cartManager->findSessionCart($session);
        if ($cart !== null) {
            try {
                $orderManager->createOrderFromCart($cart, $user);
                $session->remove(CartManager::SESSION_KEY);
                $this->addFlash('success', 'Order created');
            } catch (InsufficientStockException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->redirectToRoute('main_cart_show');
    }
}
