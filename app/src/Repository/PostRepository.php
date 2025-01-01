<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Repository\Traits\PaginationTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Clock\ClockInterface;

class PostRepository extends ServiceEntityRepository implements PaginationRepository
{
    use PaginationTrait;

    private ClockInterface $clock;

    public function __construct(
        ManagerRegistry $registry,
        ClockInterface $clock,
    ) {
        parent::__construct($registry, Post::class);

        $this->clock = $clock;
    }

    public function createQueryBuilder(string $alias, ?string $indexBy = null): QueryBuilder
    {
        $qb = parent::createQueryBuilder($alias, $indexBy);
        $qb->andWhere($alias.'.publishedAt <= :now')
            ->setParameter('now', $this->clock->now());

        return $qb;
    }
}
