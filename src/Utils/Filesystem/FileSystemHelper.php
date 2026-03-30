<?php

namespace App\Utils\Filesystem;

use Symfony\Component\Filesystem\Filesystem;

class FileSystemHelper
{
    public function __construct(private Filesystem $fileSystem)
    {
    }

    public function createFolder(string $folder): void
    {
        if (!$this->fileSystem->exists($folder)) {
            $this->fileSystem->mkdir($folder);
        }
    }

    public function remove(string $item): void
    {
        if ($this->fileSystem->exists($item)) {
            $this->fileSystem->remove($item);
        }
    }

}

