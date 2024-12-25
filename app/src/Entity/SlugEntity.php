<?php

declare(strict_types=1);

namespace App\Entity;

interface SlugEntity
{
    public function getSlug(): string;

    public function setSlug(string $slug): static;
}
