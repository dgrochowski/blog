<?php

declare(strict_types=1);

namespace App\Bus\Query;

final readonly class GetCountQuery implements QueryInterface
{
    public function __construct(
        public string $className,
    ) {
    }
}
