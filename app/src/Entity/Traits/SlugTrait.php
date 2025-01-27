<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait SlugTrait
{
    #[Groups(['api'])]
    #[ORM\Column(length: 100, unique: true)]
    protected ?string $slug = null;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = substr($slug, 0, 100);

        return $this;
    }
}
