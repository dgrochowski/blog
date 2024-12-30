<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\UpdateAdminCommand;
use App\Bus\Command\UpdateAdminCommandHandler;
use App\Entity\Admin;
use App\Repository\AdminRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UpdateAdminCommandHandlerTest extends TestCase
{
    private AdminRepository|MockObject $adminRepository;
    private UserPasswordHasherInterface|MockObject $passwordHasher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRepository = $this->createMock(AdminRepository::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
    }

    public function testUpdateAdminCommandHandlerEntityNotFound(): void
    {
        $this->adminRepository->expects(self::once())
            ->method('find')
            ->with(123)
            ->willReturn(null);

        $this->passwordHasher->expects(self::never())
            ->method('hashPassword');

        $command = new UpdateAdminCommand(
            id: 123,
            name: 'Admin',
            email: 'test@test.test',
            password: 'password',
        );

        new UpdateAdminCommandHandler(
            $this->adminRepository,
            $this->passwordHasher,
        )($command);
    }

    public function testUpdateAdminCommandHandlerWithoutPassword(): void
    {
        $admin = new Admin();
        $admin->setName('Admin');
        $admin->setEmail('test@test.test');

        $this->adminRepository->expects(self::once())
            ->method('find')
            ->with(123)
            ->willReturn($admin);

        $this->passwordHasher->expects(self::never())
            ->method('hashPassword');

        $admin->setName('New Admin');
        $admin->setEmail('admin@admin.admin');

        $command = new UpdateAdminCommand(
            id: 123,
            name: 'New Admin',
            email: 'admin@admin.admin',
            password: null,
        );

        new UpdateAdminCommandHandler(
            $this->adminRepository,
            $this->passwordHasher,
        )($command);
    }

    public function testUpdateAdminCommandHandlerWithPassword(): void
    {
        $admin = new Admin();
        $admin->setName('Admin');
        $admin->setEmail('test@test.test');
        $admin->setPassword('password');

        $this->adminRepository->expects(self::once())
            ->method('find')
            ->with(123)
            ->willReturn($admin);

        $this->passwordHasher->expects(self::once())
            ->method('hashPassword')
            ->with($admin, 'password')
            ->willReturn('hashed-password');

        $admin->setName('New Admin');
        $admin->setEmail('admin@admin.admin');
        $admin->setPassword('hashed-password');

        $command = new UpdateAdminCommand(
            id: 123,
            name: 'New Admin',
            email: 'admin@admin.admin',
            password: 'password',
        );

        new UpdateAdminCommandHandler(
            $this->adminRepository,
            $this->passwordHasher,
        )($command);
    }
}
