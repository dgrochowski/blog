<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\UpdateCategoryCommand;
use App\Bus\Command\UpdateCategoryCommandHandler;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateCategoryCommandHandlerTest extends TestCase
{
    private CategoryRepository|MockObject $categoryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = $this->createMock(CategoryRepository::class);
    }

    public function testUpdateCategoryCommandHandlerNothingToUpdate(): void
    {
        $this->categoryRepository->expects(self::once())
            ->method('find')
            ->with(321)
            ->willReturn(null);

        $command = new UpdateCategoryCommand(
            id: 321,
            name: 'Test Category',
            slug: 'old-slug',
        );

        new UpdateCategoryCommandHandler(
            $this->categoryRepository,
        )($command);
    }

    public function testUpdateCategoryCommandHandler(): void
    {
        $category = new Category();
        $category->setName('Test Category');
        $category->setSlug('old-slug');

        $this->categoryRepository->expects(self::once())
            ->method('find')
            ->with(321)
            ->willReturn($category);

        $category->setName('New Test Category');
        $category->setSlug('new-slug');

        $command = new UpdateCategoryCommand(
            id: 321,
            name: 'New Test Category',
            slug: 'new-slug',
        );

        new UpdateCategoryCommandHandler(
            $this->categoryRepository,
        )($command);
    }
}
