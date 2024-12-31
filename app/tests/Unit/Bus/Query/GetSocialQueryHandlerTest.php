<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetSocialQuery;
use App\Bus\Query\GetSocialQueryHandler;
use App\Entity\Social;
use App\Repository\SocialRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetSocialQueryHandlerTest extends TestCase
{
    private SocialRepository|MockObject $socialRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->socialRepository = $this->createMock(SocialRepository::class);
    }

    public function testGetSocialQueryHandler(): void
    {
        $social = new Social();
        $social->setName('Test name');
        $social->setName('Test Value');
        $social->setSlug('test-slug');

        $this->socialRepository->expects(self::once())
            ->method('findOneBySlug')
            ->with('test-slug')
            ->willReturn($social);

        $query = new GetSocialQuery(
            slug: 'test-slug',
        );

        $result = new GetSocialQueryHandler($this->socialRepository)($query);
        $this->assertEquals($social, $result);
    }
}
