<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetCategoriesQuery;
use PHPUnit\Framework\TestCase;

final class GetCategoriesQueryTest extends TestCase
{
    public function testGetCategoriesQuery(): void
    {
        $query = new GetCategoriesQuery();

        $this->assertEquals(true, true); // no tests yet
    }
}
