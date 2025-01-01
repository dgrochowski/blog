<?php

declare(strict_types=1);

namespace App\Bus\Query;

final readonly class GetPaginateQuery implements QueryInterface
{
    public function __construct(
        public string $className,
        public int $page,
        public int $limit,
    ) {
    }
}
