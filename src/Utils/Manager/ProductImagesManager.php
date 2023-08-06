<?php


namespace App\Utils\Manager;


use App\Entity\ProductImage;
use App\Utils\File\ImageResizer;
use App\Utils\Filesystem\FileSystemHelper;
use Doctrine\ORM\EntityManagerInterface;

class ProductImagesManager
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var FileSystemHelper
     */
    private FileSystemHelper $fileSystem;

    /**
     * @var string
     */
    private string $uploadTempDir;

    private ImageResizer $imageResizer;


    /**
     * ProductImagesManager constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FileSystemHelper $fileSystem,
        ImageResizer $imageResizer,
        string $uploadTempDir
    ) {
        $this->entityManager = $entityManager;
        $this->fileSystem = $fileSystem;
        $this->uploadTempDir = $uploadTempDir;
        $this->imageResizer = $imageResizer;
    }

    /**
     * @param  string  $productDir
     * @param  string  $tempImageFileName
     *
     * @return ProductImage
     */
    public function saveImageForProduct(
        string $productDir,
        string $tempImageFileName
    ): ProductImage {
        $this->fileSystem->createFolder($productDir);
        $fileNameId = uniqid('', false);

        $imageSmallParam = [
            'width'       => 60,
            'height'      => null,
            'newFolder'   => $productDir,
            'newFilename' => sprintf('%s_%s.jpg', $fileNameId, 'small'),
        ];

        $imageSmall = $this->imageResizer->resizeImageAndSave(
            $this->uploadTempDir,
            $tempImageFileName,
            $imageSmallParam
        );

        $imageMiddleParam = [
            'width'       => 430,
            'height'      => null,
            'newFolder'   => $productDir,
            'newFilename' => sprintf('%s_%s.jpg', $fileNameId, 'middle'),
        ];
        $imageMiddle = $this->imageResizer->resizeImageAndSave(
            $this->uploadTempDir,
            $tempImageFileName,
            $imageMiddleParam
        );

        $imageBigParam = [
            'width'       => 800,
            'height'      => null,
            'newFolder'   => $productDir,
            'newFilename' => sprintf('%s_%s.jpg', $fileNameId, 'big'),
        ];
        $imageBig = $this->imageResizer->resizeImageAndSave(
            $this->uploadTempDir,
            $tempImageFileName,
            $imageBigParam
        );

        $productImage = new ProductImage();
        $productImage->setFileNameSmall($imageSmall);
        $productImage->setFileNameMiddle($imageMiddle);
        $productImage->setFileNameBig($imageBig);

        return $productImage;
    }

    /**
     * @param  ProductImage  $productImage
     * @param  string        $productDir
     */
    public function removeImageFromProduct(
        ProductImage $productImage,
        string $productDir
    ): void {
        $smallFilePath = $productDir.'/'.$productImage->getFileNameSmall();
        $this->fileSystem->remove($smallFilePath);

        $middleFilePath = $productDir.'/'.$productImage->getFileNameMiddle();
        $this->fileSystem->remove($middleFilePath);

        $bigFilePath = $productDir.'/'.$productImage->getFileNameBig();
        $this->fileSystem->remove($bigFilePath);

        $product = $productImage->getProduct();
        $product->removeProductImage($productImage);
        $this->entityManager->flush();
    }

}