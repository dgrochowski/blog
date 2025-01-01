<?php

declare(strict_types=1);

namespace App\Bus\Query;

use App\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;

class GetBySlugQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(GetBySlugQuery $query): ?Entity
    {
        /** @phpstan-ignore-next-line */
        $repository = $this->entityManager->getRepository($query->className);

        return $repository->createQueryBuilder('e')
            ->andWhere('e.slug = :slug')
            ->setParameter('slug', $query->slug)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
