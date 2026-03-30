<?php

namespace App\Controller\Main;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category/{slug}', name: 'main_category_show')]
    public function show(Category $category): Response
    {
        $products = $category->getProducts()->getValues();

        return $this->render('main/category/show.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
