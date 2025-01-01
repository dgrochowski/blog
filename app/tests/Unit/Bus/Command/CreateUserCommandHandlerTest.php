<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\CreateUserCommand;
use App\Bus\Command\CreateUserCommandHandler;
use App\Entity\User;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateUserCommandHandlerTest extends TestCase
{
    private EntityManagerInterface|MockObject $entityManager;
    private SlugService|MockObject $slugService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->slugService = $this->createMock(SlugService::class);
    }

    public function testCreateUserCommandHandler(): void
    {
        $this->slugService->expects(self::once())
            ->method('unique')
            ->willReturn('test-post');

        $user = new User();
        $user->setName('User');
        $user->setEmail('admin@example.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setUpdatedPassword('plain-password');
        $user->setSlug('test-post');

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
            $this->slugService,
        )($command);
    }

    public function testCreateUserCommandHandlerThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('unable to persist');

        $this->slugService->expects(self::once())
            ->method('unique')
            ->willReturn('test-post');

        $user = new User();
        $user->setName('User');
        $user->setEmail('admin@example.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setUpdatedPassword('plain-password');
        $user->setSlug('test-post');

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
            $this->slugService,
        )($command);
    }
}
