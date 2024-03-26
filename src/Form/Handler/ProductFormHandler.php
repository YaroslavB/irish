<?php

namespace App\Form\Handler;

use App\Entity\Product;
use App\Form\DTO\EditFormDto;
use App\Utils\File\FileSaver;
use App\Utils\Manager\ProductManager;
use Symfony\Component\Form\Form;

class ProductFormHandler
{

    private FileSaver $fileSaver;
    private ProductManager $productManager;

    /**
     * ProductFormHandler constructor.
     */
    public function __construct(
        ProductManager $productManager,
        FileSaver $fileSaver
    ) {
        $this->fileSaver = $fileSaver;
        $this->productManager = $productManager;
    }

    /**
     * @param EditFormDto $editFormDto
     * @param Form        $form
     *
     * @return Product
     */
    public function processEditForm(
        EditFormDto $editFormDto,
        Form $form
    ): Product {
        $product = new Product();
        if ($editFormDto->id) {
            $product = $this->productManager->find($editFormDto->id);
        }
        $product->setTitle($editFormDto->title);
        $product->setPrice($editFormDto->price);
        $product->setQuantity($editFormDto->quantity);
        $product->setDescription($editFormDto->description);
        $product->setCategory($editFormDto->category);
        $product->setIsPublished($editFormDto->isPublished);
        $product->setIsDeleted($editFormDto->isDeleted);

        $this->productManager->save($product);
        $newFile = $form->get('newImage')->getData();

        $newTempFile = $newFile
            ? $this->fileSaver->saveUploadedFileTemp($newFile)
            : null;

        if ($newTempFile !== null) {
            $this->productManager->updateProductImages($product, $newTempFile);
        }

        $this->productManager->save($product);

        return $product;
    }
}
