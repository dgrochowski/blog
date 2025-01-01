<?php

declare(strict_types=1);

namespace App\Repository;

interface PaginationRepository
{
    public function countTotalRecords(): int;

    /**
     * @phpstan-ignore-next-line
     */
    public function getPage(int $currentPage, int $pageSize): array;
}
