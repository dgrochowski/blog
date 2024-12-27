<?php

declare(strict_types=1);

namespace App\Bus\Command;

final readonly class UpdateCategoryCommand implements CommandInterface
{
    public function __construct(
        public string $name,
        public string $oldSlug,
        public ?string $newSlug,
    ) {
    }
}
