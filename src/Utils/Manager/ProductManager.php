<?php


namespace App\Utils\Manager;


use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductManager
{


    /**
     * @var string
     */
    private string $productImagesDir;

    private EntityManagerInterface $entityManager;


    /**
     * ProductManager constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        string $productImagesDir
    ) {
        $this->productImagesDir = $productImagesDir;
        $this->entityManager = $entityManager;
    }

    /**
     * @return ObjectRepository
     */
    public function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Product::class);
    }

    public function remove()
    {
        //
    }

    /**
     * @param  Product  $product
     *
     * @return string
     */
    public function getProductImagesDir(Product $product): string
    {
        return sprintf('%s/%s', $this->productImagesDir, $product->getId());
    }


    /**
     * @param  Product  $product
     */
    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }


    public function updateProductImages(
        Product $product,
        string $tempImageFileName = null
    ): Product {
        if ( ! $tempImageFileName) {
            return $product;
        }
        $productDir = $this->getProductImagesDir($product);
        dd($productDir);
    }
}