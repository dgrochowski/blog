<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetSettingsQuery;
use App\Bus\Query\GetSettingsQueryHandler;
use App\Entity\Setting;
use App\Repository\SettingRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetSettingsQueryHandlerTest extends TestCase
{
    private SettingRepository|MockObject $settingRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settingRepository = $this->createMock(SettingRepository::class);
    }

    public function testGetSettingsQueryHandler(): void
    {
        $setting = new Setting();
        $setting->setName('Test name');
        $setting->setValue('Test value');
        $setting->setSlug('test-slug');

        $this->settingRepository->expects(self::once())
            ->method('findBy')
            ->with([], ['name' => 'ASC'])
            ->willReturn([$setting]);

        $query = new GetSettingsQuery();

        $result = new GetSettingsQueryHandler($this->settingRepository)($query);
        $this->assertEquals([$setting], $result);
    }
}
