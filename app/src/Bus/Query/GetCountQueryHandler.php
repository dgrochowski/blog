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

        $qb = $repository->createQueryBuilder('e')
            ->select('COUNT(e.id)');

        foreach ($query->filter as $filterName => $filterValues) {
            switch ($filterName) {
                case 'category':
                    $qb->join('e.category', 'c')
                        ->andWhere($qb->expr()->in('c.slug', ':categorySlugs'))
                        ->setParameter('categorySlugs', $filterValues);
                    break;
                case 'author':
                    $qb->join('e.author', 'a')
                        ->andWhere($qb->expr()->in('a.slug', ':authorSlugs'))
                        ->setParameter('authorSlugs', $filterValues);
                    break;
            }
        }

        $search = $query->search;
        if (null !== $search) {
            $qb->andWhere($qb->expr()->like('e.name', ':searchTerm'))
                ->setParameter('searchTerm', '%'.$search.'%');
            $qb->orWhere($qb->expr()->like('e.cachedTags', ':searchTerm'))
                ->setParameter('searchTerm', '%'.$search.'%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
