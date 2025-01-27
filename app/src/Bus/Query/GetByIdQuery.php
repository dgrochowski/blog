<?php

declare(strict_types=1);

namespace App\Bus\Query;

final readonly class GetByIdQuery implements QueryInterface
{
    public function __construct(
        public string $className,
        public int $id,
    ) {
    }
}
