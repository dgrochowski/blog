<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\DeleteCommand;
use App\Bus\Command\DeleteCommandHandler;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteCommandHandlerTest extends TestCase
{
    private TagRepository|MockObject $tagRepository;
    private EntityManagerInterface|MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tagRepository = $this->createMock(TagRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testDeleteCommandHandlerNotRemoveEntity(): void
    {
        $this->tagRepository->expects(self::once())
            ->method('find')
            ->with(123)
            ->willReturn(null);

        $this->entityManager->expects(self::once())
            ->method('getRepository')
            ->with(Tag::class)
            ->willReturn($this->tagRepository);

        $this->entityManager->expects(self::never())
            ->method('remove');

        $command = new DeleteCommand(
            className: Tag::class,
            id: 123,
        );

        new DeleteCommandHandler(
            $this->entityManager,
        )($command);
    }

    public function testDeleteCommandHandler(): void
    {
        $tag = new Tag();
        $tag->setName('Test Tag');
        $tag->setSlug('test-tag');

        $this->tagRepository->expects(self::once())
            ->method('find')
            ->with(123)
            ->willReturn($tag);

        $this->entityManager->expects(self::once())
            ->method('getRepository')
            ->with(Tag::class)
            ->willReturn($this->tagRepository);

        $this->entityManager->expects(self::once())
            ->method('remove')
            ->with($tag);

        $command = new DeleteCommand(
            className: Tag::class,
            id: 123,
        );

        new DeleteCommandHandler(
            $this->entityManager,
        )($command);
    }
}
