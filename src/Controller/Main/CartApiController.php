<?php

namespace App\Controller\Main;


use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Repository\CartProductRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="main_api_")
 */
class CartApiController extends AbstractController
{

    /**
     * @Route("/cart",methods="POST", name="cart_save")
     */
    public function saveCart(
        Request $request,
        ProductRepository $productRepository,
        ManagerRegistry $doctrine,
        CartRepository $cartRepository,
        CartProductRepository $cartProductRepository
    ): Response {
        $productUuid = $request->request->get('productId');
        $sessionId = $request->cookies->get('PHPSESSID');

        $product = $productRepository->findOneBy(['uuid' => $productUuid]);
        $cart = $cartRepository->findOneBy(['sessionId' => $sessionId]);
        if (!$cart) {
            $cart = new Cart();
            $cart->setSessionId($sessionId);
        }
        $cartProduct = $cartProductRepository->findOneBy(
            ['cart' => $cart, 'product' => $product]
        );

        if (!$cartProduct) {
            $cartProduct = new CartProduct();
            $cartProduct->setCart($cart);
            $cartProduct->setQuatity(1);
            $cartProduct->setProduct($product);
        } else {
            $cartProduct->setQuatity($cartProduct->getQuatity() + 1);
        }

        $cart->addProduct($cartProduct);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($cart);
        $entityManager->persist($cartProduct);
        $entityManager->flush();

        return new JsonResponse(['status' => false, 'data' => ['test' => 123]]);
    }
}
