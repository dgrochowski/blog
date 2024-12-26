<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetTagQuery;
use App\Bus\Query\GetTagQueryHandler;
use App\Entity\Tag;
use App\Repository\TagRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetTagQueryHandlerTest extends TestCase
{
    private TagRepository&MockObject $tagRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tagRepository = $this->createMock(TagRepository::class);
    }

    public function testGetTagQueryHandler(): void
    {
        $tag = new Tag();
        $tag->setName('Test name');
        $tag->setSlug('test-slug');

        $this->tagRepository->expects(self::once())
            ->method('findOneBySlug')
            ->with('test-slug')
            ->willReturn($tag);

        $query = new GetTagQuery(
            slug: 'test-slug',
        );

        $result = new GetTagQueryHandler($this->tagRepository)($query);
        $this->assertEquals($tag, $result);
    }
}
