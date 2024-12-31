<?php

declare(strict_types=1);

namespace App\Bus\Command;

final readonly class CreateSettingCommand implements CommandInterface
{
    public function __construct(
        public string $name,
        public ?string $value,
        public ?string $slug = null,
    ) {
    }
}
