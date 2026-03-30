<?php

namespace App\Utils\Manager;

use App\Entity\ProductImage;
use App\Utils\File\ImageResizer;
use App\Utils\Filesystem\FileSystemHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductImagesManager extends AbstractManager
{
    private const IMAGE_PATTERN = '%s_%s.jpg';

    private FileSystemHelper $fileSystem;
    private string $uploadTempDir;
    private ImageResizer $imageResizer;

    public function __construct(
        EntityManagerInterface $entityManager,
        FileSystemHelper $fileSystem,
        ImageResizer $imageResizer,
        string $uploadTempDir
    ) {
        parent::__construct($entityManager);
        $this->fileSystem = $fileSystem;
        $this->uploadTempDir = $uploadTempDir;
        $this->imageResizer = $imageResizer;
    }

    public function saveImageForProduct(
        string $productDir,
        string $tempImageFileName
    ): ProductImage {
        $this->fileSystem->createFolder($productDir);
        $fileNameId = uniqid('', false);

        $imageSmall = $this->imageResizer->resizeImageAndSave(
            $this->uploadTempDir,
            $tempImageFileName,
            [
                'width' => 60,
                'height' => null,
                'newFolder' => $productDir,
                'newFilename' => sprintf(self::IMAGE_PATTERN, $fileNameId, 'small'),
            ]
        );

        $imageMiddle = $this->imageResizer->resizeImageAndSave(
            $this->uploadTempDir,
            $tempImageFileName,
            [
                'width' => 430,
                'height' => null,
                'newFolder' => $productDir,
                'newFilename' => sprintf(self::IMAGE_PATTERN, $fileNameId, 'middle'),
            ]
        );

        $imageBig = $this->imageResizer->resizeImageAndSave(
            $this->uploadTempDir,
            $tempImageFileName,
            [
                'width' => 800,
                'height' => null,
                'newFolder' => $productDir,
                'newFilename' => sprintf(self::IMAGE_PATTERN, $fileNameId, 'big'),
            ]
        );

        $productImage = new ProductImage();
        $productImage->setFileNameSmall($imageSmall);
        $productImage->setFileNameMiddle($imageMiddle);
        $productImage->setFileNameBig($imageBig);

        return $productImage;
    }

    public function removeImageFromProduct(
        ProductImage $productImage,
        string $productDir
    ): void {
        $smallFilePath = $productDir . '/' . $productImage->getFileNameSmall();
        $this->fileSystem->remove($smallFilePath);

        $middleFilePath = $productDir . '/' . $productImage->getFileNameMiddle();
        $this->fileSystem->remove($middleFilePath);

        $bigFilePath = $productDir . '/' . $productImage->getFileNameBig();
        $this->fileSystem->remove($bigFilePath);

        $product = $productImage->getProduct();
        $product->removeProductImage($productImage);
        $this->entityManager->flush();
    }

    public function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(ProductImage::class);
    }
}

