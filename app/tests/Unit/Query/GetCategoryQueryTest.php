<?php

declare(strict_types=1);

namespace App\Tests\Unit\Query;

use App\Query\GetCategoryQuery;
use PHPUnit\Framework\TestCase;

final class GetCategoryQueryTest extends TestCase
{
    /**
     * @return iterable<string, array{
     *     slug: string,
     * }>
     */
    public function categoryData(): iterable
    {
        yield 'with slug' => [
            'slug' => 'test-category',
        ];

        yield 'with empty slug' => [
            'slug' => '',
        ];
    }

    /**
     * @dataProvider categoryData
     */
    public function testGetCategoryQuery(string $slug): void
    {
        $query = new GetCategoryQuery($slug);

        $this->assertEquals($slug, $query->slug);
    }
}
