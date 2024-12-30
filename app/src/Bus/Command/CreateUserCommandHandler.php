<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CreateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $user = new User();
        $user->setName($command->name);
        $user->setEmail($command->email);
        $user->setUpdatedPassword($command->updatedPassword);
        $user->setRoles($command->roles);

        $this->entityManager->persist($user);
    }
}
