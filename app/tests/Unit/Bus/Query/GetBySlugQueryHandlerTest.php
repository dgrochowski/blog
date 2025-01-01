<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetBySlugQuery;
use App\Bus\Query\GetBySlugQueryHandler;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetBySlugQueryHandlerTest extends TestCase
{
    private EntityManagerInterface|MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testGetBySlugQueryHandler(): void
    {
        $tag = new Tag();
        $tag->setName('Test name');
        $tag->setSlug('test-slug');

        $query = $this->createMock(Query::class);
        $query->expects(self::once())
            ->method('getOneOrNullResult')
            ->willReturn($tag);

        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects(self::once())
            ->method('andWhere')
            ->with('e.slug = :slug')
            ->willReturn($qb);
        $qb->expects(self::once())
            ->method('setParameter')
            ->with('slug', 'test-slug')
            ->willReturn($qb);
        $qb->expects(self::once())
            ->method('getQuery')
            ->willReturn($query);

        $tagRepository = $this->createMock(TagRepository::class);
        $tagRepository->expects(self::once())
            ->method('createQueryBuilder')
            ->willReturn($qb);

        $this->entityManager->expects(self::once())
            ->method('getRepository')
            ->with(Tag::class)
            ->willReturn($tagRepository);

        $query = new GetBySlugQuery(
            className: Tag::class,
            slug: 'test-slug',
        );

        $result = new GetBySlugQueryHandler($this->entityManager)($query);
        $this->assertEquals($tag, $result);
    }
}
