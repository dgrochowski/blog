<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetSettingQuery;
use App\Bus\Query\GetSettingQueryHandler;
use App\Entity\Setting;
use App\Repository\SettingRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetSettingQueryHandlerTest extends TestCase
{
    private SettingRepository|MockObject $settingRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settingRepository = $this->createMock(SettingRepository::class);
    }

    public function testGetSettingQueryHandler(): void
    {
        $setting = new Setting();
        $setting->setName('Test name');
        $setting->setName('Test Value');
        $setting->setSlug('test-slug');

        $this->settingRepository->expects(self::once())
            ->method('findOneBySlug')
            ->with('test-slug')
            ->willReturn($setting);

        $query = new GetSettingQuery(
            slug: 'test-slug',
        );

        $result = new GetSettingQueryHandler($this->settingRepository)($query);
        $this->assertEquals($setting, $result);
    }
}
