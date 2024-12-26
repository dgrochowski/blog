<?php

declare(strict_types=1);

namespace App\Tests\Unit\Query;

use App\Query\GetTagsQuery;
use PHPUnit\Framework\TestCase;

final class GetTagsQueryTest extends TestCase
{
    public function testGetTagsQuery(): void
    {
        $query = new GetTagsQuery();

        $this->assertEquals(true, true); // no tests yet
    }
}
