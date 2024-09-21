<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\DTO\EditCategoryDto;
use App\Form\EditFormCategoryType;
use App\Form\Handler\CategoryFormHandler;
use App\Repository\CategoryRepository;
use App\Utils\Manager\CategoryManager;
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
        $categories = $categoryRepository->findBy(['isDeleted' => false],
            ['id' => 'DESC']);

        return $this->render(
            'admin/category/list.html.twig',
            ['categories' => $categories]
        );
    }

    /**
     * @param Request             $request
     * @param CategoryFormHandler $categoryFormHandler
     * @param Category|null       $category
     *
     * @return Response
     * @Route("/edit/{id}", name="edit")
     * @Route("/add", name="add")
     */
    public function edit(
        Request $request,
        CategoryFormHandler $categoryFormHandler,
        Category $category = null
    ): Response {
        $editCategoryDto = EditCategoryDto::makeFromCategory($category);
        $form = $this->createForm(
            EditFormCategoryType::class,
            $editCategoryDto
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $categoryFormHandler->processEditForm($editCategoryDto);
            $this->addFlash('success', 'Category  saved');
            return $this->redirectToRoute(
                'admin_category_edit',
                ['id' => $category->getId()]
            );
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Category not saved');
        }

        return $this->render('admin/category/edit.html.twig', [
            'form'     => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * Delete  product
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(
        Category $category,
        CategoryManager $categoryManager
    ): Response
    {
        $categoryManager->remove($category);
        $this->addFlash('warning', 'Product delete');
        return $this->redirectToRoute('admin_category_list');
    }


}
