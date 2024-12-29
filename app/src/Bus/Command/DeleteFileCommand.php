<?php

declare(strict_types=1);

namespace App\Bus\Command;

final readonly class DeleteFileCommand implements CommandInterface
{
    public function __construct(
        public int $id,
    ) {
    }
}
