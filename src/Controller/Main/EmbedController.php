<?php

namespace App\Controller\Main;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class EmbedController extends AbstractController
{
    public function showLastProducts(
        ProductRepository $productRepository,
        int $productCount = 4,
        int $categoryId = null
    ): Response {
        $params = [];
        if ($categoryId) {
            $params['category'] = $categoryId;
        }
        $products = $productRepository->findBy(
            $params,
            ['id' => 'DESC'],
            $productCount
        );

        return $this->render('main/_embed/_last_products.html.twig', [
            'product' => $products,
        ]);
    }
}
