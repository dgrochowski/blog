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

    public function upload(UploadedFile $uploadedFile): File
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

        try {
            $directory = $this->directory($file);
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

    public function directory(File $file): string
    {
        $imageOrFileDirectory = $file->getIsImage() ? $this->imagesDirectory : $this->filesDirectory;
        $path = rtrim($imageOrFileDirectory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        $createdAt = $file->getCreatedAt() ?? $this->clock->now();
        $dir = $createdAt->format('ymd');

        return $path.$dir;
    }

    public function fullPath(File $file): string
    {
        return rtrim($this->directory($file), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$file->getFilename();
    }

    public function delete(File $file): void
    {
        $filePath = $this->fullPath($file);

        if (!file_exists($filePath)) {
            throw new FileServiceException('File not found: '.$filePath);
        }

        try {
            if (!unlink($filePath)) {
                throw new FileServiceException('Failed to delete file: '.$filePath);
            }
        } catch (\Throwable $e) {
            throw new FileServiceException('An error occurred while deleting the file: '.$e->getMessage());
        }
    }

    public function isImage(string $mimeType): bool
    {
        $explodedMimeType = explode('/', $mimeType);

        return 'image' === $explodedMimeType[0];
    }
}
