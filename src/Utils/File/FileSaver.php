<?php


namespace App\Utils\File;


use App\Utils\Filesystem\FileSystemHelper;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileSaver
{
    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    /**
     * @var string
     */
    private string $uploadTempDir;
    private FileSystemHelper $systemHelper;


    /**
     * FileSaver constructor.
     *
     * @param  SluggerInterface  $slugger
     * @param  FileSystemHelper  $systemHelper
     * @param  string            $uploadTempDir
     */
    public function __construct(
        SluggerInterface $slugger,
        FileSystemHelper $systemHelper,
        string $uploadTempDir
    ) {
        $this->slugger = $slugger;
        $this->uploadTempDir = $uploadTempDir;
        $this->systemHelper = $systemHelper;
    }

    /**
     * @param  UploadedFile  $uploadFile
     *
     * @return string
     */
    public function saveUploadedFileTemp(UploadedFile $uploadFile): string
    {
        $origName = pathinfo(
            $uploadFile->getClientOriginalName(),
            PATHINFO_FILENAME
        );
        $safeFileName = $this->slugger->slug($origName);
        $fileName = sprintf(
            '%s-%s.%s',
            $safeFileName,
            uniqid('', false),
            $uploadFile->guessExtension()
        );

        // check if folder exist before upload file
        $this->systemHelper->createFolder($this->uploadTempDir);

        try {
            $uploadFile->move($this->uploadTempDir, $fileName);
        } catch (FileException  $exception) {
            return $exception->getMessage();
        }

        return $fileName;
    }


}