<?php


namespace App\Utils\Manager;


use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductManager extends AbstractManager
{


    /**
     * @var string
     */
    private string $productImagesDir;
    private ProductImagesManager $images_manager;


    /**
     * ProductManager constructor.
     */
    public function __construct(

        EntityManagerInterface $entityManager,
        ProductImagesManager $images_manager,
        string $productImagesDir
    ) {
        parent::__construct($entityManager);
        $this->productImagesDir = $productImagesDir;
        $this->entityManager = $entityManager;
        $this->images_manager = $images_manager;
    }

    public function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Product::class);
    }

    /**
     * @param Product $product
     *
     * @return string
     */
    public function getProductImagesDir(Product $product): string
    {
        return sprintf('%s/%s', $this->productImagesDir, $product->getId());
    }


    /**
     * @param object $product
     *
     * @return void
     */
    public function remove(object $product): void
    {
        $product->setIsDeleted(true);
        $this->save($product);
    }


    /**
     * @param Product $product
     * @param string  $tempImageFileName
     *
     * @return Product
     */
    public function updateProductImages(
        Product $product,
        string $tempImageFileName
    ): Product {
        if (!$tempImageFileName) {
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
