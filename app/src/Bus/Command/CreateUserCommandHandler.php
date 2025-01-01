<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Entity\User;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;

class CreateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SlugService $slugService,
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $uniqueSlug = $this->slugService->unique(User::class, $command->slug ?? $command->name);

        $user = new User();
        $user->setName($command->name);
        $user->setEmail($command->email);
        $user->setUpdatedPassword($command->updatedPassword);
        $user->setRoles($command->roles);
        $user->setSlug($uniqueSlug);

        $this->entityManager->persist($user);
    }
}
