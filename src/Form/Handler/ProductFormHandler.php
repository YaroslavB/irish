<?php

namespace App\Form\Handler;

use App\Entity\Product;
use App\Utils\File\FileSaver;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Form;

class ProductFormHandler
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $entityManager;
    private FileSaver $fileSaver;

    /**
     * ProductFormHandler constructor.
     */
    public function __construct(
        ManagerRegistry $entityManager,
        FileSaver $fileSaver
    ) {
        $this->entityManager = $entityManager;
        $this->fileSaver = $fileSaver;
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
        $newFile = $form->get('newImage')->getData();
        $newTempFile = $newFile
            ? $this->fileSaver->saveUploadedFileTemp($newFile)
            : null;

        $entityManager->persist($product);
        $entityManager->flush();

        return $product;
    }


}