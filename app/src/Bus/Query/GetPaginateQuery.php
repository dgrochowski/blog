<?php

declare(strict_types=1);

namespace App\Bus\Query;

final readonly class GetPaginateQuery implements QueryInterface
{
    /**
     * @param array<string, string[]> $filter
     * @param array<string, string>   $order
     */
    public function __construct(
        public string $className,
        public int $page,
        public int $limit,
        public array $filter = [],
        public ?string $search = null,
        public array $order = [],
    ) {
    }
}
