<?php

namespace App\Tests\Unit\Utils\Filesystem;

use App\Utils\Filesystem\FileSystemHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class FileSystemHelperTest extends TestCase
{
    private FileSystemHelper $fileSystemHelper;
    private MockObject&Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->fileSystemHelper = new FileSystemHelper($this->filesystem);
    }

    public function testCreateFolderWhenFolderDoesNotExist(): void
    {
        $folder = '/tmp/test-folder';

        $this->filesystem->expects($this->once())
            ->method('exists')
            ->with($folder)
            ->willReturn(false);

        $this->filesystem->expects($this->once())
            ->method('mkdir')
            ->with($folder);

        $this->fileSystemHelper->createFolder($folder);
    }

    public function testCreateFolderWhenFolderAlreadyExists(): void
    {
        $folder = '/tmp/existing-folder';

        $this->filesystem->expects($this->once())
            ->method('exists')
            ->with($folder)
            ->willReturn(true);

        $this->filesystem->expects($this->never())
            ->method('mkdir');

        $this->fileSystemHelper->createFolder($folder);
    }

    public function testRemoveWhenItemExists(): void
    {
        $item = '/tmp/file-to-remove.txt';

        $this->filesystem->expects($this->once())
            ->method('exists')
            ->with($item)
            ->willReturn(true);

        $this->filesystem->expects($this->once())
            ->method('remove')
            ->with($item);

        $this->fileSystemHelper->remove($item);
    }

    public function testRemoveWhenItemDoesNotExist(): void
    {
        $item = '/tmp/non-existent-file.txt';

        $this->filesystem->expects($this->once())
            ->method('exists')
            ->with($item)
            ->willReturn(false);

        $this->filesystem->expects($this->never())
            ->method('remove');

        $this->fileSystemHelper->remove($item);
    }
}

