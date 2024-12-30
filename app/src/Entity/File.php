<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\SlugTrait;
use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity as TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File implements Entity, SlugEntity, TimestampableEntity
{
    use SlugTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isImage;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private string $fileName;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private string $originalName;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private string $directory;

    #[ORM\Column(type: 'integer')]
    private int $size;

    #[ORM\Column(length: 100)]
    private string $mimeType;

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
        return $this->size;
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
}
