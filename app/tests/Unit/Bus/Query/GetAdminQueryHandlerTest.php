<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetAdminQuery;
use App\Bus\Query\GetAdminQueryHandler;
use App\Entity\Admin;
use App\Repository\AdminRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetAdminQueryHandlerTest extends TestCase
{
    private AdminRepository&MockObject $adminRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRepository = $this->createMock(AdminRepository::class);
    }

    public function testGetAdminQueryHandler(): void
    {
        $admin = new Admin();
        $admin->setName('Admin');
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('hashed-password');

        $this->adminRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('admin@example.com')
            ->willReturn($admin);

        $query = new GetAdminQuery(
            email: 'admin@example.com',
        );

        $result = new GetAdminQueryHandler($this->adminRepository)($query);
        $this->assertEquals($admin, $result);
    }
}
