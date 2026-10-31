<?php

declare(strict_types=1);

namespace App\Controller\Main;

use App\Repository\ProductRepository;
use App\Utils\Manager\CartManager;
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
        CartManager $cartManager
    ): Response {
        $product = $productRepository->findOneBy([
            'uuid'        => $request->request->get('productId'),
            'isDeleted'   => false,
            'isPublished' => true,
        ]);
        if (!$product) {
            return new JsonResponse(
                ['status' => false, 'error' => 'Product not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $cart = $cartManager->addProduct($request->getSession(), $product);

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
