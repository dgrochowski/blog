<?php

declare(strict_types=1);

namespace App\Repository\Traits;

trait PaginationTrait
{
    public function countTotalRecords(): int
    {
        $query = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    /**
     * @phpstan-ignore-next-line
     */
    public function getPage(int $currentPage, int $pageSize): array
    {
        return $this->createQueryBuilder('e')
            ->setFirstResult(($currentPage - 1) * $pageSize)
            ->setMaxResults($pageSize)
            ->getQuery()
            ->getResult();
    }
}
