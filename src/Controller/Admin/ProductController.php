<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\Admin\EditFormProductType;
use App\Form\DTO\EditFormDto;
use App\Form\Handler\ProductFormHandler;
use App\Repository\ProductRepository;
use App\Utils\Manager\ProductManager;
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
        ProductFormHandler $formHandler,
        Product $product = null
    ): Response
    {
        $editProductDto = EditFormDto::fromProduct($product);
        $form = $this->createForm(EditFormProductType::class, $editProductDto);

        // dd($editProductDto, $form,$request,$form->getData());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $formHandler->processEditForm($editProductDto, $form);
            $this->addFlash('success', 'Product saved');

            return $this->redirectToRoute(
                'admin_product_edit',
                ['id' => $product->getId()]
            );
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Product not saved');
        }

        $images = $product
            ? $product->getProductImages()->getValues()
            : [];

        return $this->render(
            'admin/product/edit.html.twig',
            [
                'images' => $images,
                'form' => $form->createView(),
                'product' => $product,
            ]
        );
    }

    /**
     * Delete  product
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(
        Product $product,
        ProductManager $productManager
    ): Response {
        $productManager->remove($product);
        $this->addFlash('success', 'Product deleted');
        return $this->redirectToRoute('admin_product_list');
    }
}
