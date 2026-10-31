<?php

declare(strict_types=1);

namespace App\Controller\Main;

use App\Entity\Category;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    public const PER_PAGE = 12;

    #[Route('/category/{slug}', name: 'main_category_show')]
    public function show(Category $category, Request $request, ProductRepository $productRepository): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        // Fetch one extra row to know whether a next page exists without a COUNT query
        $products = $productRepository->findBy(
            ['category' => $category, 'isDeleted' => false, 'isPublished' => true],
            ['id' => 'DESC'],
            self::PER_PAGE + 1,
            ($page - 1) * self::PER_PAGE
        );

        return $this->render('main/category/show.html.twig', [
            'category' => $category,
            'products' => array_slice($products, 0, self::PER_PAGE),
            'page'     => $page,
            'hasNext'  => count($products) > self::PER_PAGE,
        ]);
    }
}
