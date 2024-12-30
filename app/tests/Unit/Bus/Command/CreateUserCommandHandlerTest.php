<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\CreateUserCommand;
use App\Bus\Command\CreateUserCommandHandler;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateUserCommandHandlerTest extends TestCase
{
    private EntityManagerInterface|MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testCreateUserCommandHandler(): void
    {
        $user = new User();
        $user->setName('User');
        $user->setEmail('admin@example.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setUpdatedPassword('plain-password');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($user);

        $command = new CreateUserCommand(
            name: 'User',
            email: 'admin@example.com',
            updatedPassword: 'plain-password',
            roles: ['ROLE_ADMIN'],
        );

        new CreateUserCommandHandler(
            $this->entityManager,
        )($command);
    }

    public function testCreateUserCommandHandlerThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('unable to persist');

        $user = new User();
        $user->setName('User');
        $user->setEmail('admin@example.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setUpdatedPassword('plain-password');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($user)
            ->willThrowException(new \Exception('unable to persist'));

        $command = new CreateUserCommand(
            name: 'User',
            email: 'admin@example.com',
            updatedPassword: 'plain-password',
            roles: ['ROLE_ADMIN'],
        );

        new CreateUserCommandHandler(
            $this->entityManager,
        )($command);
    }
}
