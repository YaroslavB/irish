<?php

namespace App\Form\Handler;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Form;

class ProductFormHandler
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $entityManager;

    /**
     * ProductFormHandler constructor.
     */
    public function __construct(ManagerRegistry $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param  Product  $product
     * @param  Form     $form
     *
     * @return Product
     */
    public function processEditForm(Product $product, Form $form): Product
    {
        $entityManager = $this->entityManager->getManager();
        $entityManager->persist($product);
        $entityManager->flush();

        return $product;
    }


}