<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetByIdQuery;
use App\Bus\Query\GetByIdQueryHandler;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetByIdQueryHandlerTest extends TestCase
{
    private EntityManagerInterface|MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testGetByIdQueryHandler(): void
    {
        $tag = new Tag();
        $tag->setName('Test name');
        $tag->setSlug('test-slug');

        $tagRepository = $this->createMock(TagRepository::class);
        $tagRepository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($tag);

        $this->entityManager->expects(self::once())
            ->method('getRepository')
            ->with(Tag::class)
            ->willReturn($tagRepository);

        $query = new GetByIdQuery(
            id: 1,
            className: Tag::class,
        );

        $result = new GetByIdQueryHandler($this->entityManager)($query);
        $this->assertEquals($tag, $result);
    }
}
