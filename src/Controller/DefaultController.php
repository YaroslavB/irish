<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\EditFormProductType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $productList = $entityManager
            ->getRepository(Product::class)
            ->findAll();

        //dd($productList);


        return $this->render(
            'main/default/index.html.twig',
            [
                'controller_name' => 'DefaultController',
            ]
        );
    }

    /**
     * Edit product data
     * @Route("/edit-product/{id}", name="edit_product", methods="GET|POST",requirements={"id"="\d+"})
     * @Route("/add-product", name="add_product", methods="GET|POST")
     *
     * @param  Request  $request
     * @param  ManagerRegistry  $doctrine
     * @param  int|null  $id
     *
     * @return Response
     */
    public function editProduct(
        Request $request,
        ManagerRegistry $doctrine,
        int $id = null
    ): Response {
        $entityManager = $doctrine->getManager();
        if ($id) {
            $product = $entityManager
                ->getRepository(Product::class)
                ->find($id);
        } else {
            $product = new Product();
        }
        $form = $this->createForm(EditFormProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute(
                'edit_product',
                ['id' => $product->getId()]
            );
        }


        return $this->render(
            'main/default/edit_product.html.twig',
            ['form' => $form->createView()]
        );
    }
}
