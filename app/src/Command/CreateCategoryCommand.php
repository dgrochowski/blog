<?php

declare(strict_types=1);

namespace App\Command;

final class CreateCategoryCommand
{
    public function __construct(
        private string $name,
        private ?string $slug = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }
}
