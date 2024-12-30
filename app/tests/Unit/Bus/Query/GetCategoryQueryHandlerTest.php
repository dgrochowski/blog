<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetCategoryQuery;
use App\Bus\Query\GetCategoryQueryHandler;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetCategoryQueryHandlerTest extends TestCase
{
    private CategoryRepository|MockObject $categoryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = $this->createMock(CategoryRepository::class);
    }

    public function testGetCategoryQueryHandler(): void
    {
        $category = new Category();
        $category->setName('Test name');
        $category->setSlug('test-slug');

        $this->categoryRepository->expects(self::once())
            ->method('findOneBySlug')
            ->with('test-slug')
            ->willReturn($category);

        $query = new GetCategoryQuery(
            slug: 'test-slug',
        );

        $result = new GetCategoryQueryHandler($this->categoryRepository)($query);
        $this->assertEquals($category, $result);
    }
}
