<?php

declare(strict_types=1);

namespace App\Bus\Query;

final readonly class GetBySlugQuery implements QueryInterface
{
    public function __construct(
        public string $className,
        public string $slug,
    ) {
    }
}
