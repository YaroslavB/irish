<?php

namespace App\Tests\Unit\Utils\File;

use Symfony\Component\HttpFoundation\File\File;
use App\Utils\File\FileSaver;
use App\Utils\Filesystem\FileSystemHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class FileSaverTest extends TestCase
{
    private FileSaver $fileSaver;
    private MockObject&SluggerInterface $slugger;
    private MockObject&FileSystemHelper $fileSystemHelper;
    private string $uploadTempDir = '/tmp/uploads';

    protected function setUp(): void
    {
        $this->slugger = $this->createMock(SluggerInterface::class);
        $this->fileSystemHelper = $this->createMock(FileSystemHelper::class);

        $this->fileSaver = new FileSaver(
            $this->slugger,
            $this->fileSystemHelper,
            $this->uploadTempDir
        );
    }

    public function testSaveUploadedFileTempSuccess(): void
    {
        $uploadedFile = $this->createMock(UploadedFile::class);
        
        $uploadedFile->expects($this->once())
            ->method('getClientOriginalName')
            ->willReturn('test-file.jpg');

        $uploadedFile->expects($this->once())
            ->method('guessExtension')
            ->willReturn('jpg');

        $uploadedFile->expects($this->once())
            ->method('move')
            ->with($this->uploadTempDir, $this->matchesRegularExpression('/^test-file-[a-f0-9]+\.jpg$/'));

        $sluggedName = new UnicodeString('test-file');
        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('test-file')
            ->willReturn($sluggedName);

        $this->fileSystemHelper->expects($this->once())
            ->method('createFolder')
            ->with($this->uploadTempDir);

        $result = $this->fileSaver->saveUploadedFileTemp($uploadedFile);

        $this->assertMatchesRegularExpression('/^test-file-[a-f0-9]+\.jpg$/', $result);
    }

    public function testSaveUploadedFileTempWithFileException(): void
    {
        $uploadedFile = $this->createMock(UploadedFile::class);
        $errorMessage = 'Unable to write file';

        $uploadedFile->expects($this->once())
            ->method('getClientOriginalName')
            ->willReturn('document.pdf');

        $uploadedFile->expects($this->once())
            ->method('guessExtension')
            ->willReturn('pdf');

        $uploadedFile->expects($this->once())
            ->method('move')
            ->willThrowException(new FileException($errorMessage));

        $sluggedName = new UnicodeString('document');
        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('document')
            ->willReturn($sluggedName);

        $this->fileSystemHelper->expects($this->once())
            ->method('createFolder')
            ->with($this->uploadTempDir);

        $result = $this->fileSaver->saveUploadedFileTemp($uploadedFile);

        $this->assertSame($errorMessage, $result);
    }

    public function testSaveUploadedFileTempWithSpecialCharactersInFilename(): void
    {
        $uploadedFile = $this->createMock(UploadedFile::class);

        $uploadedFile->expects($this->once())
            ->method('getClientOriginalName')
            ->willReturn('My Spëcial Fîlé (1).png');

        $uploadedFile->expects($this->once())
            ->method('guessExtension')
            ->willReturn('png');

        $uploadedFile->expects($this->once())
            ->method('move');

        $sluggedName = new UnicodeString('my-special-file-1');
        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('My Spëcial Fîlé (1)')
            ->willReturn($sluggedName);

        $this->fileSystemHelper->expects($this->once())
            ->method('createFolder')
            ->with($this->uploadTempDir);

        $result = $this->fileSaver->saveUploadedFileTemp($uploadedFile);

        $this->assertMatchesRegularExpression('/^my-special-file-1-[a-f0-9]+\.png$/', $result);
    }

    public function testSaveUploadedFileTempWithNoExtension(): void
    {
        $uploadedFile = $this->createMock(UploadedFile::class);

        $uploadedFile->expects($this->once())
            ->method('getClientOriginalName')
            ->willReturn('file-without-extension');

        $uploadedFile->expects($this->once())
            ->method('guessExtension')
            ->willReturn(null);

        $uploadedFile->expects($this->once())
            ->method('move');

        $sluggedName = new UnicodeString('file-without-extension');
        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('file-without-extension')
            ->willReturn($sluggedName);

        $this->fileSystemHelper->expects($this->once())
            ->method('createFolder');

        $result = $this->fileSaver->saveUploadedFileTemp($uploadedFile);

        $this->assertMatchesRegularExpression('/^file-without-extension-[a-f0-9]+\.$/', $result);
    }

    public function testFolderIsCreatedBeforeFileMove(): void
    {
        $uploadedFile = $this->createMock(UploadedFile::class);
        $callOrder = [];

        $uploadedFile->method('getClientOriginalName')->willReturn('test.jpg');
        $uploadedFile->method('guessExtension')->willReturn('jpg');

        $this->slugger->method('slug')->willReturn(new UnicodeString('test'));

        $this->fileSystemHelper->expects($this->once())
            ->method('createFolder')
            ->willReturnCallback(function () use (&$callOrder) {
                $callOrder[] = 'createFolder';
            });

        $uploadedFile->expects($this->once())
            ->method('move')
            ->willReturnCallback(function () use (&$callOrder) {
                $callOrder[] = 'move';
                return $this->createMock(File::class);
            });

        $this->fileSaver->saveUploadedFileTemp($uploadedFile);

        $this->assertSame(['createFolder', 'move'], $callOrder);
    }
}

