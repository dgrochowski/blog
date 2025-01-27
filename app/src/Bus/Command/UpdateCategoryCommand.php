<?php

declare(strict_types=1);

namespace App\Bus\Command;

final readonly class UpdateCategoryCommand implements CommandInterface
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
    ) {
    }
}
