<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\UpdateTagCommand;
use App\Bus\Command\UpdateTagCommandHandler;
use App\Entity\Tag;
use App\Repository\TagRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateTagCommandHandlerTest extends TestCase
{
    private TagRepository|MockObject $tagRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tagRepository = $this->createMock(TagRepository::class);
    }

    public function testUpdateTagCommandHandlerNothingToUpdate(): void
    {
        $this->tagRepository->expects(self::once())
            ->method('find')
            ->with(321)
            ->willReturn(null);

        $command = new UpdateTagCommand(
            id: 321,
            name: 'Test Tag',
            slug: 'old-slug',
        );

        new UpdateTagCommandHandler(
            $this->tagRepository,
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

        $command = new UpdateTagCommand(
            id: 321,
            name: 'New Test Tag',
            slug: 'new-slug',
        );

        new UpdateTagCommandHandler(
            $this->tagRepository,
        )($command);
    }
}
