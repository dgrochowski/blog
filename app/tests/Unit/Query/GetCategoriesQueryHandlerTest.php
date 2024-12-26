<?php

declare(strict_types=1);

namespace App\Tests\Unit\Query;

use App\Entity\Category;
use App\Query\GetCategoriesQuery;
use App\Query\GetCategoriesQueryHandler;
use App\Repository\CategoryRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetCategoriesQueryHandlerTest extends TestCase
{
    private CategoryRepository&MockObject $categoryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = $this->createMock(CategoryRepository::class);
    }

    public function testGetCategoriesQueryHandler(): void
    {
        $category = new Category();
        $category->setName('Test name');
        $category->setSlug('test-slug');

        $this->categoryRepository->expects(self::once())
            ->method('findBy')
            ->with([], ['name' => 'ASC'])
            ->willReturn([$category]);

        $query = new GetCategoriesQuery();

        $result = new GetCategoriesQueryHandler($this->categoryRepository)($query);
        $this->assertEquals([$category], $result);
    }
}
