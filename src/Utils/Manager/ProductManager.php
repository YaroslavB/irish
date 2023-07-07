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
    private ProductImagesManager $images_manager;


    /**
     * ProductManager constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ProductImagesManager $images_manager,
        string $productImagesDir
    ) {
        $this->productImagesDir = $productImagesDir;
        $this->entityManager = $entityManager;
        $this->images_manager = $images_manager;
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


    /**
     * @param  Product  $product
     * @param  string   $tempImageFileName
     *
     * @return Product
     */
    public function updateProductImages(
        Product $product,
        string $tempImageFileName
    ): Product {
        if ( ! $tempImageFileName) {
            return $product;
        }
        // product save directory
        $productDir = $this->getProductImagesDir($product);

        $productImages = $this->images_manager->saveImageForProduct(
            $productDir,
            $tempImageFileName
        );
        $productImages->setProduct($product);
        $product->addProductImage($productImages);

        return $product;
    }
}