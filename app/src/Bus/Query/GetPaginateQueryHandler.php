<?php

declare(strict_types=1);

namespace App\Bus\Query;

use App\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;

class GetPaginateQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return Entity[]
     */
    public function __invoke(GetPaginateQuery $query): array
    {
        /** @phpstan-ignore-next-line */
        $repository = $this->entityManager->getRepository($query->className);

        return $repository->createQueryBuilder('e')
            ->setFirstResult(($query->page - 1) * $query->limit)
            ->setMaxResults($query->limit)
            ->getQuery()
            ->getResult();
    }
}
