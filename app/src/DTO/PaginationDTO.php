<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

readonly class PaginationDTO
{
    public function __construct(
        #[Groups(['api'])]
        public int $page,

        #[Groups(['api'])]
        public int $totalPages,

        #[Groups(['api'])]
        public int $limit,

        /**
         * @var array<int, object>
         */
        #[Groups(['api'])]
        public array $data,
    ) {
    }
}
