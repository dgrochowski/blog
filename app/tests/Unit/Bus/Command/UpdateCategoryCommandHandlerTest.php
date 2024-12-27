<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\UpdateCategoryCommand;
use App\Bus\Command\UpdateCategoryCommandHandler;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateCategoryCommandHandlerTest extends TestCase
{
    private CategoryRepository&MockObject $categoryRepository;
    private EntityManagerInterface&MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testUpdateCategoryCommandHandlerNothigToUpdate(): void
    {
        $this->categoryRepository->expects(self::once())
            ->method('findOneBySlug')
            ->with('old-slug')
            ->willReturn(null);

        $this->entityManager->expects(self::never())
            ->method('persist');

        $command = new UpdateCategoryCommand(
            name: 'Test Category',
            oldSlug: 'old-slug',
            newSlug: 'new-slug',
        );

        new UpdateCategoryCommandHandler(
            $this->categoryRepository,
            $this->entityManager,
        )($command);
    }

    public function testUpdateCategoryCommandHandlerNullableNewSlug(): void
    {
        $category = new Category();
        $category->setName('Test Category');
        $category->setSlug('slug');

        $this->categoryRepository->expects(self::once())
            ->method('findOneBySlug')
            ->with('slug')
            ->willReturn($category);

        $category->setName('New Test Category');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($category);

        $command = new UpdateCategoryCommand(
            name: 'New Test Category',
            oldSlug: 'slug',
            newSlug: null,
        );

        new UpdateCategoryCommandHandler(
            $this->categoryRepository,
            $this->entityManager,
        )($command);
    }

    public function testUpdateCategoryCommandHandlerNotUpdateSlug(): void
    {
        $category1 = new Category();
        $category1->setName('Test Category1');
        $category1->setSlug('slug1');

        $category2 = new Category();
        $category2->setName('Test Category2');
        $category2->setSlug('slug2');

        $this->categoryRepository->expects(self::exactly(2))
            ->method('findOneBySlug')
            ->willReturnOnConsecutiveCalls($category1, $category2);

        $category1->setName('New Test Category1');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($category1);

        $command = new UpdateCategoryCommand(
            name: 'New Test Category1',
            oldSlug: 'slug1',
            newSlug: 'slug2',
        );

        new UpdateCategoryCommandHandler(
            $this->categoryRepository,
            $this->entityManager,
        )($command);
    }

    public function testUpdateCategoryCommandHandler(): void
    {
        $category = new Category();
        $category->setName('Test Category');
        $category->setSlug('old-slug');

        $this->categoryRepository->expects(self::exactly(2))
            ->method('findOneBySlug')
            ->willReturnOnConsecutiveCalls($category, null);

        $category->setName('New Test Category');
        $category->setSlug('new-slug');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($category);

        $command = new UpdateCategoryCommand(
            name: 'New Test Category',
            oldSlug: 'old-slug',
            newSlug: 'new-slug',
        );

        new UpdateCategoryCommandHandler(
            $this->categoryRepository,
            $this->entityManager,
        )($command);
    }
}
