<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\File;
use App\Exception\FileServiceException;
use Psr\Clock\ClockInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileService
{
    public function __construct(
        private string $imagesDirectory,
        private string $filesDirectory,
        private SluggerInterface $slugger,
        private ClockInterface $clock,
    ) {
    }

    public function upload(UploadedFile $uploadedFile, string $slug): File
    {
        $extension = $uploadedFile->guessExtension();
        $mimeType = $uploadedFile->getMimeType();
        $originalFilename = $uploadedFile->getClientOriginalName();
        $safeFilename = $this->slugger->slug($originalFilename)->lower()->toString();
        $fileName = $safeFilename.'-'.uniqid().'.'.$extension;

        $file = new File();
        $file->setIsImage($this->isImage($mimeType));
        $file->setFileName($fileName);
        $file->setOriginalName($originalFilename);
        $file->setSize($uploadedFile->getSize());
        $file->setMimeType($mimeType);
        $file->setDirectory($this->clock->now()->format('ymd'));
        $file->setSlug($slug);

        try {
            $directory = $this->fullDirectory($file);
            if (!is_dir($directory)) {
                if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
                    throw new FileServiceException('Failed to create directory: '.$directory);
                }
            }
            $uploadedFile->move($directory, $fileName);
        } catch (\Throwable $e) {
            throw new FileServiceException('Failed to upload file: '.$e->getMessage());
        }

        return $file;
    }

    public function delete(File $file): void
    {
        $filePath = $this->fullPath($file);
        $directoryPath = dirname($filePath);

        if (false === file_exists($filePath)) {
            throw new FileServiceException('File not found: '.$filePath);
        }

        try {
            if (false === unlink($filePath)) {
                throw new FileServiceException('Failed to delete file: '.$filePath);
            }
            if (is_dir($directoryPath) && $this->isDirectoryEmpty($directoryPath)) {
                if (false === rmdir($directoryPath)) {
                    throw new FileServiceException('Failed to delete directory: '.$directoryPath);
                }
            }
        } catch (\Throwable $e) {
            throw new FileServiceException('An error occurred while deleting the file: '.$e->getMessage());
        }
    }

    private function isImage(string $mimeType): bool
    {
        $explodedMimeType = explode('/', $mimeType);

        return 'image' === $explodedMimeType[0];
    }

    private function fullPath(File $file): string
    {
        return rtrim($this->fullDirectory($file), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$file->getFilename();
    }

    private function fullDirectory(File $file): string
    {
        $imageOrFileDirectory = $file->getIsImage() ? $this->imagesDirectory : $this->filesDirectory;
        $path = rtrim($imageOrFileDirectory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        return $path.$file->getDirectory();
    }

    private function isDirectoryEmpty(string $directoryPath): bool
    {
        $contents = scandir($directoryPath);
        // Remove "." and ".." from the contents
        $contents = array_diff($contents, ['.', '..']);

        return empty($contents);
    }
}
