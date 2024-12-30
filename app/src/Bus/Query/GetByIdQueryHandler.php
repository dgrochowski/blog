<?php

declare(strict_types=1);

namespace App\Bus\Query;

use App\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;

class GetByIdQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(GetByIdQuery $query): ?Entity
    {
        /** @phpstan-ignore-next-line */
        $repository = $this->entityManager->getRepository($query->className);

        return $repository->find($query->id);
    }
}
