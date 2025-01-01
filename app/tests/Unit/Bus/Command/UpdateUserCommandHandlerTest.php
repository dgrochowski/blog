<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\UpdateUserCommand;
use App\Bus\Command\UpdateUserCommandHandler;
use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateUserCommandHandlerTest extends TestCase
{
    private UserRepository|MockObject $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
    }

    public function testUpdateUserCommandHandlerEntityNotFound(): void
    {
        $this->userRepository->expects(self::once())
            ->method('find')
            ->with(123)
            ->willReturn(null);

        $command = new UpdateUserCommand(
            id: 123,
            name: 'User',
            email: 'test@test.test',
            updatedPassword: 'password',
            roles: ['ROLE_ADMIN'],
            slug: 'test-slug',
        );

        new UpdateUserCommandHandler(
            $this->userRepository,
        )($command);
    }

    public function testUpdateUserCommandHandlerWithoutPassword(): void
    {
        $user = new User();
        $user->setName('User');
        $user->setEmail('test@test.test');
        $user->setSlug('some-slug');

        $this->userRepository->expects(self::once())
            ->method('find')
            ->with(123)
            ->willReturn($user);

        $user->setName('New User');
        $user->setEmail('admin@admin.admin');
        $user->setSlug('test-slug');

        $command = new UpdateUserCommand(
            id: 123,
            name: 'New User',
            email: 'admin@admin.admin',
            updatedPassword: null,
            roles: ['ROLE_ADMIN'],
            slug: 'test-slug',
        );

        new UpdateUserCommandHandler(
            $this->userRepository,
        )($command);
    }

    public function testUpdateUserCommandHandlerWithPassword(): void
    {
        $user = new User();
        $user->setName('User');
        $user->setEmail('test@test.test');
        $user->setPassword('password');
        $user->setSlug('some-slug');

        $this->userRepository->expects(self::once())
            ->method('find')
            ->with(123)
            ->willReturn($user);

        $user->setName('New User');
        $user->setEmail('admin@admin.admin');
        $user->setUpdatedPassword('plain-password');
        $user->setSlug('test-slug');

        $command = new UpdateUserCommand(
            id: 123,
            name: 'New User',
            email: 'admin@admin.admin',
            updatedPassword: 'plain-password',
            roles: ['ROLE_ADMIN'],
            slug: 'test-slug',
        );

        new UpdateUserCommandHandler(
            $this->userRepository,
        )($command);
    }
}
