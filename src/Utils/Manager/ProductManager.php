<?php

namespace App\Utils\Manager;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductManager extends AbstractManager
{
    private string $productImagesDir;
    private ProductImagesManager $imagesManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProductImagesManager $imagesManager,
        string $productImagesDir
    ) {
        parent::__construct($entityManager);
        $this->productImagesDir = $productImagesDir;
        $this->imagesManager = $imagesManager;
    }

    public function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Product::class);
    }

    public function getProductImagesDir(Product $product): string
    {
        return sprintf('%s/%s', $this->productImagesDir, $product->getId());
    }

    /**
     * @param Product $product
     */
    public function remove(object $product): void
    {
        $product->setIsDeleted(true);
        $this->save($product);
    }

    public function updateProductImages(
        Product $product,
        string $tempImageFileName
    ): Product {
        if (!$tempImageFileName) {
            return $product;
        }

        $productDir = $this->getProductImagesDir($product);
        $productImages = $this->imagesManager->saveImageForProduct(
            $productDir,
            $tempImageFileName
        );
        $productImages->setProduct($product);
        $product->addProductImage($productImages);

        return $product;
    }

}

