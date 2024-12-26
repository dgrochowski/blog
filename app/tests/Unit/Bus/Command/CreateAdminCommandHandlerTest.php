<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\CreateAdminCommand;
use App\Bus\Command\CreateAdminCommandHandler;
use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class CreateAdminCommandHandlerTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;
    private UserPasswordHasherInterface&MockObject $passwordHasher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
    }

    public function testCreateAdminCommandHandler(): void
    {
        $this->passwordHasher->expects(self::once())
            ->method('hashPassword')
            ->willReturn('hashed-password');

        $admin = new Admin();
        $admin->setName('Admin');
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('hashed-password');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($admin);

        $command = new CreateAdminCommand(
            name: 'Admin',
            email: 'admin@example.com',
            roles: ['ROLE_ADMIN'],
            password: 'plain-password',
        );

        new CreateAdminCommandHandler(
            $this->entityManager,
            $this->passwordHasher,
        )($command);
    }

    public function testCreateTagCommandHandlerThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('unable to persist');

        $this->passwordHasher->expects(self::once())
            ->method('hashPassword')
            ->willReturn('hashed-password');

        $admin = new Admin();
        $admin->setName('Admin');
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('hashed-password');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($admin)
            ->willThrowException(new \Exception('unable to persist'));

        $command = new CreateAdminCommand(
            name: 'Admin',
            email: 'admin@example.com',
            roles: ['ROLE_ADMIN'],
            password: 'plain-password',
        );

        new CreateAdminCommandHandler(
            $this->entityManager,
            $this->passwordHasher,
        )($command);
    }
}
