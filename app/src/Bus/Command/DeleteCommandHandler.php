<?php

declare(strict_types=1);

namespace App\Bus\Command;

use Doctrine\ORM\EntityManagerInterface;

class DeleteCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(DeleteCommand $command): void
    {
        /** @phpstan-ignore-next-line */
        $repository = $this->entityManager->getRepository($command->className);
        $entity = $repository->find($command->id);

        if (null !== $entity) {
            $this->entityManager->remove($entity);
        }
    }
}
