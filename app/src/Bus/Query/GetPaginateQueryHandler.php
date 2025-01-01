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

        $qb = $repository->createQueryBuilder('e')
            ->setFirstResult(($query->page - 1) * $query->limit)
            ->setMaxResults($query->limit);

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

        foreach ($query->order as $field => $order) {
            $qb->addOrderBy('e.'.$field, $order);
        }

        return $qb->getQuery()->getResult();
    }
}
