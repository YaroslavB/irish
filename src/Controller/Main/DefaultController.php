<?php

namespace App\Controller\Main;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        return $this->render(
            'main/default/index.html.twig',
            [
                'controller_name' => 'DefaultController',
            ]
        );
    }

}
