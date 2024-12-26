<?php

declare(strict_types=1);

namespace App\Bus\Query;

final readonly class GetCategoryQuery implements QueryInterface
{
    public function __construct(
        public string $slug,
    ) {
    }
}
