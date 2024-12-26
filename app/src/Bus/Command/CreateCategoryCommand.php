<?php

declare(strict_types=1);

namespace App\Bus\Command;

final readonly class CreateCategoryCommand implements CommandInterface
{
    public function __construct(
        public string $name,
        public ?string $slug = null,
    ) {
    }
}
