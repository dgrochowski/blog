<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetAdminQuery;
use PHPUnit\Framework\TestCase;

final class GetAdminQueryTest extends TestCase
{
    /**
     * @return iterable<string, array{
     *     email: string,
     * }>
     */
    public function adminData(): iterable
    {
        yield 'with email' => [
            'email' => 'admin@example.com',
        ];

        yield 'with empty email' => [
            'email' => '',
        ];
    }

    /**
     * @dataProvider adminData
     */
    public function testGetAdminQuery(string $email): void
    {
        $query = new GetAdminQuery($email);

        $this->assertEquals($email, $query->email);
    }
}
