<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\Admin\EditFormProductType;
use App\Form\DTO\EditFormDto;
use App\Form\Handler\ProductFormHandler;
use App\Repository\ProductRepository;
use App\Utils\Manager\ProductManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/product', name: 'admin_product_')]
class ProductController extends AbstractController
{
    private const PER_PAGE = 30;

    #[Route(path: '/list', name: 'list')]
    public function list(Request $request, ProductRepository $productRepository): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        // Fetch one extra row to know whether a next page exists without a COUNT query
        $products = $productRepository->findBy(
            ['isDeleted' => false],
            ['id' => 'DESC'],
            self::PER_PAGE + 1,
            ($page - 1) * self::PER_PAGE
        );
        $hasNext = count($products) > self::PER_PAGE;

        return $this->render('admin/product/list.html.twig', [
            'products' => array_slice($products, 0, self::PER_PAGE),
            'page'     => $page,
            'hasNext'  => $hasNext,
        ]);
    }

    #[Route(path: '/edit/{id}', name: 'edit')]
    #[Route(path: '/add', name: 'add')]
    public function edit(
        Request $request,
        ProductFormHandler $formHandler,
        Product $product = null
    ): Response {
        $editProductDto = EditFormDto::fromProduct($product);
        $form = $this->createForm(EditFormProductType::class, $editProductDto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $product = $formHandler->processEditForm($editProductDto, $form);
                $this->addFlash('success', 'Product saved');
            } catch (FileException $exception) {
                // Handler persists the product before moving the file, so only the image is missing
                $this->addFlash('error', 'Product saved, but image upload failed: ' . $exception->getMessage());
                if ($product === null) {
                    return $this->redirectToRoute('admin_product_list');
                }
            }

            return $this->redirectToRoute('admin_product_edit', ['id' => $product->getId()]);
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

    #[Route(path: '/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Product $product,
        ProductManager $productManager
    ): Response {
        if (!$this->isCsrfTokenValid('delete-product-' . $product->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        $productManager->remove($product);
        $this->addFlash('success', 'Product deleted');
        return $this->redirectToRoute('admin_product_list');
    }
}
