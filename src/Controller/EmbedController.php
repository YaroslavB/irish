<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class EmbedController extends AbstractController
{
    public function showLastProducts(ProductRepository $productRepository
    ): Response {
        $products = $productRepository->findBy([], ['id' => 'DESC']);

        return $this->render('main/_embed/_last_products.html.twig', [
            'product' => $products,
        ]);
    }
}
