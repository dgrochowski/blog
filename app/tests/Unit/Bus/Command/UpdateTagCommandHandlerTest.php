<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\UpdateTagCommand;
use App\Bus\Command\UpdateTagCommandHandler;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateTagCommandHandlerTest extends TestCase
{
    private TagRepository&MockObject $tagRepository;
    private EntityManagerInterface&MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tagRepository = $this->createMock(TagRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testUpdateTagCommandHandlerNothingToUpdate(): void
    {
        $this->tagRepository->expects(self::once())
            ->method('find')
            ->with(321)
            ->willReturn(null);

        $this->entityManager->expects(self::never())
            ->method('persist');

        $command = new UpdateTagCommand(
            id: 321,
            name: 'Test Tag',
            slug: 'old-slug',
        );

        new UpdateTagCommandHandler(
            $this->tagRepository,
            $this->entityManager,
        )($command);
    }

    public function testUpdateTagCommandHandler(): void
    {
        $tag = new Tag();
        $tag->setName('Test Tag');
        $tag->setSlug('old-slug');

        $this->tagRepository->expects(self::once())
            ->method('find')
            ->with(321)
            ->willReturn($tag);

        $tag->setName('New Test Tag');
        $tag->setSlug('new-slug');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($tag);

        $command = new UpdateTagCommand(
            id: 321,
            name: 'New Test Tag',
            slug: 'new-slug',
        );

        new UpdateTagCommandHandler(
            $this->tagRepository,
            $this->entityManager,
        )($command);
    }
}
