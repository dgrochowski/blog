<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\File;
use Doctrine\ORM\Mapping as ORM;

trait FileTrait
{
    private const PUBLIC_IMAGES_PATH = '/uploads/images';
    private const PUBLIC_FILES_PATH = '/uploads/files';

    #[ORM\OneToOne(targetEntity: File::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinColumn(name: 'file_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?File $file = null;

    private ?string $uploadImageName = null;

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getFilePath(): ?string
    {
        if (null === $this->file) {
            return null;
        }

        $publicPath = $this->file->getIsImage() ? self::PUBLIC_IMAGES_PATH : self::PUBLIC_FILES_PATH;

        return $publicPath
            .DIRECTORY_SEPARATOR
            .$this->file->getDirectory()
            .DIRECTORY_SEPARATOR
            .$this->file->getFilename();
    }

    public function getUploadImageName(): ?string
    {
        return $this->uploadImageName;
    }

    public function setUploadImageName(?string $uploadImageName): self
    {
        $this->uploadImageName = $uploadImageName;

        return $this;
    }
}
