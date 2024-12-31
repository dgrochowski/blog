<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\SlugTrait;
use App\Repository\FileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity as TimestampableTrait;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File implements Entity, SlugEntity, TimestampableEntity
{
    use SlugTrait;
    use TimestampableTrait;
    private const PUBLIC_IMAGES_PATH = '/uploads/images';
    private const PUBLIC_FILES_PATH = '/uploads/files';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isImage;

    #[ORM\Column(length: 255)]
    private string $fileName;

    #[ORM\Column(length: 255)]
    private string $originalName;

    #[ORM\Column(length: 255)]
    private string $directory;

    #[ORM\Column(type: Types::INTEGER)]
    private int $size;

    #[ORM\Column(length: 100)]
    private string $mimeType;

    private ?string $uploadImageName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsImage(): bool
    {
        return $this->isImage;
    }

    public function setIsImage(bool $isImage): self
    {
        $this->isImage = $isImage;

        return $this;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function setDirectory(string $directory): self
    {
        $this->directory = $directory;

        return $this;
    }

    public function getSize(): int
    {
        return (int) round($this->size * 0.000001, 2); // Bytes to MB
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getFilePath(): ?string
    {
        $publicPath = $this->getIsImage() ? self::PUBLIC_IMAGES_PATH : self::PUBLIC_FILES_PATH;

        return $publicPath
            .DIRECTORY_SEPARATOR
            .$this->getDirectory()
            .DIRECTORY_SEPARATOR
            .$this->getFilename();
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
