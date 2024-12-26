<?php

declare(strict_types=1);

namespace App\Bus\Query;

final readonly class GetAdminQuery implements QueryInterface
{
    public function __construct(
        public string $email,
    ) {
    }
}
