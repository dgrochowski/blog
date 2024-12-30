<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(event: Events::preFlush, entity: User::class)]
class UserPreFlushListener
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function preFlush(User $user, PreFlushEventArgs $args): void
    {
        $updatedPassword = $user->getUpdatedPassword();
        if (null !== $updatedPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $updatedPassword);
            $user->setPassword($hashedPassword);
        }
    }
}
