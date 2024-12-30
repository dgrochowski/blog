<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\File;
use App\Exception\FileServiceException;
use App\Service\FileService;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Clock\ClockInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

final class FileServiceTest extends KernelTestCase
{
    private const IMAGES_DIRECTORY = '/tmp/blog/images';
    private const FILES_DIRECTORY = '/tmp/blog/files';
    private const UPLOADED_DIRECTORY = '/tmp/blog/uploaded';
    private SluggerInterface&MockObject $slugger;
    private ClockInterface&MockObject $clock;
    private FileService $fileService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->slugger = $this->createMock(SluggerInterface::class);
        $this->clock = $this->createMock(ClockInterface::class);

        @mkdir(directory: '/tmp/blog');
        @mkdir(directory: self::IMAGES_DIRECTORY, recursive: true);
        @mkdir(directory: self::FILES_DIRECTORY, recursive: true);
        @mkdir(directory: self::UPLOADED_DIRECTORY, recursive: true);

        $this->fileService = new FileService(
            self::IMAGES_DIRECTORY,
            self::FILES_DIRECTORY,
            $this->slugger,
            $this->clock,
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->removeDirectory('/tmp/blog');
    }

    private function removeDirectory(string $directory): void
    {
        $files = glob($directory.DIRECTORY_SEPARATOR.'*');
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->removeDirectory($file);
            } else {
                unlink($file);
            }
        }
        rmdir(directory: $directory);
    }

    public function testUploadSuccessfully(): void
    {
        $expectedDir = '241228';
        $time = new \DateTimeImmutable('2024-12-28 12:00');

        $this->slugger->expects(self::once())
            ->method('slug')
            ->with('imageFileName.jpg')
            ->willReturn(new UnicodeString('slugged-name.jpg'));

        $this->clock->expects(self::once())
            ->method('now')
            ->willReturn($time);

        $uploadedFile = $this->createImageUploadedFile('imageFileName.jpg');
        $fileSize = $uploadedFile->getSize();
        $fileSize = round($fileSize * 0.000001, 2); // Bytes to MB
        $mimeType = $uploadedFile->getMimeType();

        $file = $this->fileService->upload($uploadedFile, 'test-slug');

        $this->assertTrue(file_exists(
            self::IMAGES_DIRECTORY
            .DIRECTORY_SEPARATOR
            .$expectedDir
            .DIRECTORY_SEPARATOR
            .$file->getFileName()
        ), 'Image has not been uploaded to the expected directory');

        $this->assertEquals(true, $file->getIsImage());
        $this->assertEquals('imageFileName.jpg', $file->getOriginalName());
        $this->assertEquals($fileSize, $file->getSize());
        $this->assertEquals($mimeType, $file->getMimeType());
        $this->assertEquals('test-slug', $file->getSlug());
    }

    public function testDelete(): void
    {
        $expectedDir = '250120';
        $time = new \DateTimeImmutable('2025-01-20 12:00');

        $this->slugger->expects(self::once())
            ->method('slug')
            ->with('test.txt')
            ->willReturn(new UnicodeString('slugged-name.txt'));

        $this->clock->expects(self::once())
            ->method('now')
            ->willReturn($time);

        $uploadedFile = $this->createTextUploadedFile('test.txt');
        $file = $this->fileService->upload($uploadedFile, 'test-slug');

        $this->assertTrue(file_exists(
            self::FILES_DIRECTORY
            .DIRECTORY_SEPARATOR
            .$expectedDir
            .DIRECTORY_SEPARATOR
            .$file->getFileName()
        ), 'File has not been uploaded to the expected directory');

        $this->fileService->delete($file);

        $this->assertFalse(file_exists(
            self::FILES_DIRECTORY
            .DIRECTORY_SEPARATOR
            .$expectedDir
            .DIRECTORY_SEPARATOR
            .$file->getFileName()
        ), 'File has not been removed from the expected directory');
    }

    public function testDeleteThrowsExceptionWhenFileDoesNotExists(): void
    {
        $this->expectException(FileServiceException::class);
        $this->expectExceptionMessage(
            'File not found: '
            .self::FILES_DIRECTORY
            .DIRECTORY_SEPARATOR
            .'250125'
            .DIRECTORY_SEPARATOR
            .'textFileName.txt'
        );

        $file = new File();
        $file->setIsImage(false);
        $file->setCreatedAt(new \DateTime('2025-01-25 12:00'));
        $file->setFileName('textFileName.txt');
        $file->setDirectory('250125');

        $this->fileService->delete($file);
    }

    private function createImageUploadedFile(string $originalName): UploadedFile
    {
        $image = imagecreatetruecolor(100, 100);
        $imagePath = self::UPLOADED_DIRECTORY.DIRECTORY_SEPARATOR.$originalName;
        imagejpeg($image, $imagePath);

        return new UploadedFile(
            path: $imagePath,
            originalName: $originalName,
            test: true,
        );
    }

    private function createTextUploadedFile(string $originalName): UploadedFile
    {
        $content = 'Example file content';
        $filePath = self::UPLOADED_DIRECTORY.DIRECTORY_SEPARATOR.$originalName;
        file_put_contents($filePath, $content);

        return new UploadedFile(
            path: $filePath,
            originalName: $originalName,
            test: true,
        );
    }
}
