<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetPaginateQuery;
use App\Bus\Query\GetPaginateQueryHandler;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetPaginateQueryHandlerTest extends TestCase
{
    private EntityManagerInterface|MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testGetPaginateQueryHandler(): void
    {
        $page = 1;
        $limit = 10;

        $tag = new Tag();
        $tag->setName('Test name');
        $tag->setSlug('test-slug');

        $query = $this->createMock(Query::class);
        $query->expects(self::once())
            ->method('getResult')
            ->willReturn([$tag]);

        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects(self::once())
            ->method('setFirstResult')
            ->with(($page - 1) * $limit)
            ->willReturn($qb);
        $qb->expects(self::once())
            ->method('setMaxResults')
            ->with($limit)
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

        $query = new GetPaginateQuery(
            className: Tag::class,
            page: 1,
            limit: 10,
        );

        $result = new GetPaginateQueryHandler($this->entityManager)($query);
        $this->assertEquals([$tag], $result);
    }
}
