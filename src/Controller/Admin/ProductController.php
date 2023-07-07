<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\EditFormProductType;
use App\Form\Handler\ProductFormHandler;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/product", name="admin_product_")
 */
class ProductController extends AbstractController
{
    /**
     * Show all product
     * @Route("/list", name="list")
     */
    public function list(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(
            ['isDeleted' => false],
            ['id' => 'DESC'],
            30
        );

        return $this->render(
            'admin/product/list.html.twig',
            ['products' => $products]
        );
    }

    /**
     * Add and Edit product
     * @Route("/edit/{id}", name="edit")
     * @Route("/add", name="add")
     */
    public function edit(
        Request $request,
        ManagerRegistry $doctrine,
        ProductFormHandler $form_handler,
        Product $product = null
    ): Response {
        $form = $this->createForm(EditFormProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $form_handler->processEditForm($product, $form);

            return $this->redirectToRoute(
                'admin_product_edit',
                ['id' => $product->getId()]
            );
        }


        return $this->render(
            'admin/product/edit.html.twig',
            [
                'images'  => $product->getProductImages()->getValues(),
                'form'    => $form->createView(),
                'product' => $product,
            ]
        );
    }

    /**
     * Delete  product
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(): Response
    {
    }
}
