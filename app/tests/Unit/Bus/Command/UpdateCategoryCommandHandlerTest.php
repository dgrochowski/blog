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

    public function testUpdateCategoryCommandHandlerNothingToUpdate(): void
    {
        $this->categoryRepository->expects(self::once())
            ->method('find')
            ->with(321)
            ->willReturn(null);

        $this->entityManager->expects(self::never())
            ->method('persist');

        $command = new UpdateCategoryCommand(
            id: 321,
            name: 'Test Category',
            slug: 'old-slug',
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

        $this->categoryRepository->expects(self::once())
            ->method('find')
            ->with(321)
            ->willReturn($category);

        $category->setName('New Test Category');
        $category->setSlug('new-slug');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($category);

        $command = new UpdateCategoryCommand(
            id: 321,
            name: 'New Test Category',
            slug: 'new-slug',
        );

        new UpdateCategoryCommandHandler(
            $this->categoryRepository,
            $this->entityManager,
        )($command);
    }
}
