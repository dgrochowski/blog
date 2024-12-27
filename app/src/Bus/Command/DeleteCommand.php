<?php

declare(strict_types=1);

namespace App\Bus\Command;

final readonly class DeleteCommand implements CommandInterface
{
    public function __construct(
        public string $className,
        public int $id,
    ) {
    }
}
