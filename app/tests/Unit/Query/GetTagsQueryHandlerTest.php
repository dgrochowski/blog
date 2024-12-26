<?php

declare(strict_types=1);

namespace App\Tests\Unit\Query;

use App\Entity\Tag;
use App\Query\GetTagsQuery;
use App\Query\GetTagsQueryHandler;
use App\Repository\TagRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetTagsQueryHandlerTest extends TestCase
{
    private TagRepository&MockObject $tagRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tagRepository = $this->createMock(TagRepository::class);
    }

    public function testGetTagsQueryHandler(): void
    {
        $tag = new Tag();
        $tag->setName('Test name');
        $tag->setSlug('test-slug');

        $this->tagRepository->expects(self::once())
            ->method('findBy')
            ->with([], ['name' => 'ASC'])
            ->willReturn([$tag]);

        $query = new GetTagsQuery();

        $result = new GetTagsQueryHandler($this->tagRepository)($query);
        $this->assertEquals([$tag], $result);
    }
}
