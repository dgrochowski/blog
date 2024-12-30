<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Repository\AdminRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UpdateAdminCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private AdminRepository $adminRepository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function __invoke(UpdateAdminCommand $command): void
    {
        $admin = $this->adminRepository->find($command->id);
        if (null === $admin) {
            return;
        }

        $admin->setName($command->name);
        $admin->setEmail($command->email);

        if (null !== $command->password) {
            $hashedPassword = $this->passwordHasher->hashPassword($admin, $command->password);
            $admin->setPassword($hashedPassword);
        }
    }
}
