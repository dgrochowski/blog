<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\File;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait FileTrait
{
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

    #[Groups(['api'])]
    public function getFilePath(): ?string
    {
        if (null === $this->file) {
            return null;
        }

        return $this->file->getFilePath();
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
