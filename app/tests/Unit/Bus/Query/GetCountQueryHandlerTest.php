<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetCountQuery;
use App\Bus\Query\GetCountQueryHandler;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetCountQueryHandlerTest extends TestCase
{
    private EntityManagerInterface|MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testGetCountQueryHandler(): void
    {
        $query = $this->createMock(Query::class);
        $query->expects(self::once())
            ->method('getSingleScalarResult')
            ->willReturn(5);

        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects(self::once())
            ->method('select')
            ->with('COUNT(e.id)')
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

        $query = new GetCountQuery(
            className: Tag::class,
        );

        $result = new GetCountQueryHandler($this->entityManager)($query);
        $this->assertEquals(5, $result);
    }
}
