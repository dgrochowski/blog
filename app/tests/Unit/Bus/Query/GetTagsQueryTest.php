<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetTagsQuery;
use PHPUnit\Framework\TestCase;

final class GetTagsQueryTest extends TestCase
{
    public function testGetTagsQuery(): void
    {
        $query = new GetTagsQuery();

        $this->assertEquals(true, true); // no tests yet
    }
}
