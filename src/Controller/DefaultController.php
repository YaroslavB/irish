<?php

namespace App\Controller;

use App\Entity\Product;
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


        return $this->render('main/default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * This is test method, add sample product to database
     * @Route("/product-add",name="product add" ,methods={"GET"})
     */
    public function productAdd(Request $request,ManagerRegistry $doctrine):Response
    {
        $product = new Product();
        $product->setTitle('Product'.random_int(1, 100));
        $product->setDescription('Some product text description');
        $product->setPrice(10);
        $product->setQuantity(1);

        $entityManager =$doctrine->getManager();
        $entityManager->persist($product);
        $entityManager->flush();

        return $this->redirectToRoute('homepage');

    }
}
