<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetUserQuery;
use App\Bus\Query\GetUserQueryHandler;
use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetUserQueryHandlerTest extends TestCase
{
    private UserRepository|MockObject $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
    }

    public function testGetUserQueryHandler(): void
    {
        $user = new User();
        $user->setName('User');
        $user->setEmail('admin@example.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword('hashed-password');

        $this->userRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('admin@example.com')
            ->willReturn($user);

        $query = new GetUserQuery(
            email: 'admin@example.com',
        );

        $result = new GetUserQueryHandler($this->userRepository)($query);
        $this->assertEquals($user, $result);
    }
}
