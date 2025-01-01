<?php

declare(strict_types=1);

namespace App\Bus\Query;

use Doctrine\ORM\EntityManagerInterface;

class GetCountQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(GetCountQuery $query): int
    {
        /** @phpstan-ignore-next-line */
        $repository = $this->entityManager->getRepository($query->className);

        $result = $repository->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->getQuery();

        return (int) $result->getSingleScalarResult();
    }
}
