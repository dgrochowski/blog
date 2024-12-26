<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\CreateAdminCommand;
use PHPUnit\Framework\TestCase;

final class CreateAdminCommandTest extends TestCase
{
    /**
     * @return iterable<string, array{
     *     name: string,
     *     email: string,
     *     password: string,
     * }>
     */
    public function adminData(): iterable
    {
        yield 'with name, email and password' => [
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ];

        yield 'without name, email and password' => [
            'name' => '',
            'email' => '',
            'password' => '',
        ];
    }

    /**
     * @dataProvider adminData
     */
    public function testCreateAdminCommand(string $name, string $email, string $password): void
    {
        $command = new CreateAdminCommand($name, $email, $password);

        $this->assertEquals($name, $command->name);
        $this->assertEquals($email, $command->email);
        $this->assertEquals($password, $command->password);
    }
}
