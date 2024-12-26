<?php

declare(strict_types=1);

namespace App\Tests\Unit\Query;

use App\Query\GetTagQuery;
use PHPUnit\Framework\TestCase;

final class GetTagQueryTest extends TestCase
{
    /**
     * @return iterable<string, array{
     *     slug: string,
     * }>
     */
    public function tagData(): iterable
    {
        yield 'with slug' => [
            'slug' => 'test-tag',
        ];

        yield 'with empty slug' => [
            'slug' => '',
        ];
    }

    /**
     * @dataProvider tagData
     */
    public function testGetTagQuery(string $slug): void
    {
        $query = new GetTagQuery($slug);

        $this->assertEquals($slug, $query->slug);
    }
}
