<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Bus\Query\GetTagQuery;
use App\Entity\Tag;
use App\Service\ApiEntityHandler;
use PHPUnit\Framework\TestCase;

final class ApiEntityHandlerTest extends TestCase
{
    private ApiEntityHandler $apiEntityHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiEntityHandler = new ApiEntityHandler();
    }

    public function testHandleArrayWithStrings(): void
    {
        $this->assertEmpty($this->apiEntityHandler->handleArray([
            'string1',
            'string2',
            'string3',
        ]));
    }

    public function testHandleArrayWithNonApiObjects(): void
    {
        $this->assertEmpty($this->apiEntityHandler->handleArray([
            new GetTagQuery('string1'),
            new GetTagQuery('string2'),
            new GetTagQuery('string3'),
        ]));
    }

    public function testHandleArray(): void
    {
        $tag1 = new Tag();
        $tag1->setName('string1');
        $tag1->setSlug('string-slug1');

        $tag2 = new Tag();
        $tag2->setName('string2');
        $tag2->setSlug('string-slug2');

        $expected = [
            [
                'id' => null,
                'name' => 'string1',
                'slug' => 'string-slug1',
            ],
            [
                'id' => null,
                'name' => 'string2',
                'slug' => 'string-slug2',
            ],
        ];
        $result = $this->apiEntityHandler->handleArray([$tag1, $tag2]);

        $this->assertEquals($expected, $result);
    }

    public function testHandleNonApiObject(): void
    {
        $this->assertEmpty($this->apiEntityHandler->handleObject(
            new GetTagQuery('string1'),
        ));
    }

    public function testHandleObject(): void
    {
        $tag = new Tag();
        $tag->setName('string');
        $tag->setSlug('string-slug');

        $expected = [
            'id' => null,
            'name' => 'string',
            'slug' => 'string-slug',
        ];
        $result = $this->apiEntityHandler->handleObject($tag);

        $this->assertEquals($expected, $result);
    }
}
