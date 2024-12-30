<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\CreateCategoryCommand;
use App\Bus\Command\CreateCategoryCommandHandler;
use App\Entity\Category;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateCategoryCommandHandlerTest extends TestCase
{
    private SlugService|MockObject $slugService;
    private EntityManagerInterface|MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->slugService = $this->createMock(SlugService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testCreateCategoryCommandHandler(): void
    {
        $this->slugService->expects(self::once())
            ->method('unique')
            ->willReturn('test-category');

        $category = new Category();
        $category->setName('Test Category');
        $category->setSlug('test-category');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($category);

        $command = new CreateCategoryCommand(
            name: 'Test Category',
            slug: 'test-category',
        );

        new CreateCategoryCommandHandler(
            $this->slugService,
            $this->entityManager,
        )($command);
    }

    public function testCreateCategoryCommandHandlerThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('unable to persist');

        $this->slugService->expects(self::once())
            ->method('unique')
            ->willReturn('test-category');

        $category = new Category();
        $category->setName('Test Category');
        $category->setSlug('test-category');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->willThrowException(new \Exception('unable to persist'));

        $command = new CreateCategoryCommand(
            name: 'Test Category',
            slug: 'test-category',
        );

        new CreateCategoryCommandHandler(
            $this->slugService,
            $this->entityManager,
        )($command);
    }
}
