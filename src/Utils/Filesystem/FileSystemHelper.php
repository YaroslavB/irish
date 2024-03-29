<?php


namespace App\Utils\Filesystem;


use Symfony\Component\Filesystem\Filesystem;

class FileSystemHelper
{
    /**
     * @var Filesystem
     */
    private Filesystem $fileSystem;

    /**
     * FileSystemHelper constructor.
     */
    public function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * @param  string  $folder
     */
    public function createFolder(string $folder): void
    {
        if ( ! $this->fileSystem->exists($folder)) {
            $this->fileSystem->mkdir($folder);
        }
    }

    /**
     * @param  string  $item
     */
    public function remove(string $item): void
    {
        if ($this->fileSystem->exists($item)) {
            $this->fileSystem->remove($item);
        }
    }


}
