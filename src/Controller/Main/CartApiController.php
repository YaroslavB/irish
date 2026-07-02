<?php

namespace App\Controller\Main;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Repository\CartProductRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'main_api_')]
class CartApiController extends AbstractController
{
    #[Route('/cart', name: 'cart_save', methods: ['POST'])]
    public function saveCart(
        Request $request,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager,
        CartRepository $cartRepository,
        CartProductRepository $cartProductRepository
    ): Response {
        $productUuid = $request->request->get('productId');
        $sessionId = $request->cookies->get('PHPSESSID');

        if (!is_string($sessionId) || $sessionId === '') {
            return new JsonResponse(
                ['status' => false, 'error' => 'Session not found'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $product = $productRepository->findOneBy(['uuid' => $productUuid]);
        if (!$product) {
            return new JsonResponse(
                ['status' => false, 'error' => 'Product not found'],
                Response::HTTP_NOT_FOUND
            );
        }

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
            $cartProduct->setQuantity(1);
            $cartProduct->setProduct($product);
            $cart->addCartProduct($cartProduct);
        } else {
            $cartProduct->setQuantity($cartProduct->getQuantity() + 1);
        }

        $entityManager->persist($cart);
        $entityManager->persist($cartProduct);
        $entityManager->flush();

        return new JsonResponse([
            'status' => true,
            'message' => 'Product added to cart',
            'data' => [
                'cartId' => $cart->getId(),
                'productCount' => count($cart->getCartProducts())
            ]
        ], Response::HTTP_OK);
    }
}
