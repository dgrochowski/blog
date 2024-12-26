<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAdminCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function __invoke(CreateAdminCommand $command): void
    {
        $admin = new Admin();
        $admin->setName($command->name);
        $admin->setEmail($command->email);
        $admin->setRoles($command->roles);

        $hashedPassword = $this->passwordHasher->hashPassword($admin, $command->password);
        $admin->setPassword($hashedPassword);

        $this->entityManager->persist($admin);
    }
}
