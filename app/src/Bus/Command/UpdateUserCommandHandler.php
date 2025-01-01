<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Repository\UserRepository;

class UpdateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(UpdateUserCommand $command): void
    {
        $user = $this->userRepository->find($command->id);
        if (null === $user) {
            return;
        }

        $user->setName($command->name);
        $user->setEmail($command->email);
        $user->setUpdatedPassword($command->updatedPassword);
        $user->setRoles($command->roles);
        $user->setSlug($command->slug);
    }
}
