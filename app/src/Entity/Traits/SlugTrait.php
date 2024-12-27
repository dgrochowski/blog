<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SlugTrait
{
    #[ORM\Column(length: 100, unique: true)]
    protected ?string $slug = null;

    protected ?string $newSlug = null;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = substr($slug, 0, 100);

        return $this;
    }

    public function getNewSlug(): ?string
    {
        return $this->newSlug;
    }

    public function setNewSlug(?string $newSlug): static
    {
        $this->newSlug = $newSlug;

        return $this;
    }
}
