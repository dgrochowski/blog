<?php

declare(strict_types=1);

namespace App\Bus\Command;

final readonly class CreateFileCommand implements CommandInterface
{
    public function __construct(
        public ?string $uploadImageName,
        public ?string $slug = null,
    ) {
    }
}
