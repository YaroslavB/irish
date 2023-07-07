<?php


namespace App\Utils\File;


use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageResizer
{


    /**
     * @var Imagine
     */
    private Imagine $imagine;

    /**
     * ImageResizer constructor.
     */
    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    /**
     * @param  string  $originalFileFolder
     * @param  string  $originalFileName
     * @param  array   $targetParams
     *
     * @return string
     */
    public function resizeImageAndSave(
        string $originalFileFolder,
        string $originalFileName,
        array $targetParams
    ): string {
        $originalFilePath = $originalFileFolder.'/'.$originalFileName;
        [$imageWidth, $imageHeight] = getimagesize($originalFilePath);
        $ratio = $imageWidth / $imageHeight;
        $targetWidth = $targetParams['width'];
        $targetHeight = $targetParams['height'];
        if ($targetHeight) {
            if ($targetWidth / $targetHeight > $ratio) {
                $targetWidth = $targetHeight * $ratio;
            } else {
                $targetHeight = $targetWidth / $ratio;
            }
        } else {
            $targetHeight = $targetWidth / $ratio;
        }

        $targetFolder = $targetParams['newFolder'];
        $targetFileName = $targetParams['newFilename'];

        $targetFilePath = sprintf('%s/%s', $targetFolder, $targetFileName);

        $imagineFile = $this->imagine->open($originalFilePath);
        $imagineFile->resize(new Box($targetWidth, $targetHeight))->save(
            $targetFilePath
        );

        return $targetFileName;
    }


}