<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/admin/category", name="admin_category_")
 */
class CategoryController extends AbstractController
{
    /**
     * Show all category
     * @Route("/list", name="list")
     */
    public function list(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy(
            [],
            ['id' => 'DESC'],
            30
        );

        return $this->render(
            'admin/category/list.html.twig',
            ['categories' => $categories]
        );
    }

    public function edit(Request $request, Category $category = null): Response
    {
        return $this->render('admin/category/edit', [
            'controller_name' => 'CategoryController',
        ]);
    }

    /**
     * Delete  product
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Category $category): Response
    {
        return $this->render('admin/category/delete', [
            'controller_name' => 'CategoryController',
        ]);
    }


}
