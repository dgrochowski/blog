<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetSocialsQuery;
use App\Bus\Query\GetSocialsQueryHandler;
use App\Entity\Social;
use App\Repository\SocialRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetSocialsQueryHandlerTest extends TestCase
{
    private SocialRepository|MockObject $socialRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->socialRepository = $this->createMock(SocialRepository::class);
    }

    public function testGetSocialsQueryHandler(): void
    {
        $social = new Social();
        $social->setName('Test name');
        $social->setValue('Test value');
        $social->setSlug('test-slug');

        $this->socialRepository->expects(self::once())
            ->method('findBy')
            ->with([], ['name' => 'ASC'])
            ->willReturn([$social]);

        $query = new GetSocialsQuery();

        $result = new GetSocialsQueryHandler($this->socialRepository)($query);
        $this->assertEquals([$social], $result);
    }
}
