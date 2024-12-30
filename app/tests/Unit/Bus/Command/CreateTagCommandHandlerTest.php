<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\CreateTagCommand;
use App\Bus\Command\CreateTagCommandHandler;
use App\Entity\Tag;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateTagCommandHandlerTest extends TestCase
{
    private SlugService|MockObject $slugService;
    private EntityManagerInterface|MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->slugService = $this->createMock(SlugService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testCreateTagCommandHandler(): void
    {
        $this->slugService->expects(self::once())
            ->method('unique')
            ->willReturn('test-tag');

        $tag = new Tag();
        $tag->setName('Test Tag');
        $tag->setSlug('test-tag');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($tag);

        $command = new CreateTagCommand(
            name: 'Test Tag',
            slug: 'test-tag',
        );

        new CreateTagCommandHandler(
            $this->slugService,
            $this->entityManager,
        )($command);
    }

    public function testCreateTagCommandHandlerThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('unable to persist');

        $this->slugService->expects(self::once())
            ->method('unique')
            ->willReturn('test-tag');

        $tag = new Tag();
        $tag->setName('Test Tag');
        $tag->setSlug('test-tag');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->willThrowException(new \Exception('unable to persist'));

        $command = new CreateTagCommand(
            name: 'Test Tag',
            slug: 'test-tag',
        );

        new CreateTagCommandHandler(
            $this->slugService,
            $this->entityManager,
        )($command);
    }
}
