<?php

declare(strict_types=1);

namespace App\Query;

final readonly class GetCategoryQuery implements QueryInterface
{
    public function __construct(
        public string $slug,
    ) {
    }
}
