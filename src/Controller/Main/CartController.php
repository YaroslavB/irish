<?php

namespace App\Controller\Main;

use App\Entity\User;
use App\Repository\CartRepository;
use App\Utils\Manager\OrderManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'main_cart_show')]
    public function show(
        Request $request,
        CartRepository $cartRepository
    ): Response {
        $sessionId = $request->cookies->get('PHPSESSID');
        $cart = $cartRepository->findOneBy(['sessionId' => $sessionId]);

        return $this->render('main/cart/show.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/cart/create', name: 'main_cart_create')]
    public function create(
        Request $request,
        OrderManager $orderManager
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $sessionId = $request->cookies->get('PHPSESSID');
        if (is_string($sessionId) && $sessionId !== '') {
            $orderManager->createOrderFromCartFromSession($sessionId, $user);
        }

        return $this->redirectToRoute('main_cart_show');
    }

}
