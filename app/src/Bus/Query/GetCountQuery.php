<?php

declare(strict_types=1);

namespace App\Bus\Query;

final readonly class GetCountQuery implements QueryInterface
{
    /**
     * @param array<string, string[]> $filter
     */
    public function __construct(
        public string $className,
        public array $filter = [],
        public ?string $search = null,
    ) {
    }
}
