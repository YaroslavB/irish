<?php

namespace App\Form\Handler;

use App\Entity\Product;
use App\Utils\File\FileSaver;
use App\Utils\Manager\ProductManager;
use Symfony\Component\Form\Form;

class ProductFormHandler
{

    private FileSaver $fileSaver;
    private ProductManager $product_manager;

    /**
     * ProductFormHandler constructor.
     */
    public function __construct(
        ProductManager $product_manager,
        FileSaver $fileSaver
    ) {
        $this->fileSaver = $fileSaver;
        $this->product_manager = $product_manager;
    }

    /**
     * @param  Product  $product
     * @param  Form     $form
     *
     * @return Product
     */
    public function processEditForm(Product $product, Form $form): Product
    {
        $this->product_manager->save($product);
        $newFile = $form->get('newImage')->getData();
        $newTempFile = $newFile
            ? $this->fileSaver->saveUploadedFileTemp($newFile)
            : null;
        $this->product_manager->updateProductImages($product, $newTempFile);

        return $product;
    }


}