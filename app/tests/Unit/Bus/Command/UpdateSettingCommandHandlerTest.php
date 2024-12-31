<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\UpdateSettingCommand;
use App\Bus\Command\UpdateSettingCommandHandler;
use App\Entity\Setting;
use App\Repository\SettingRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateSettingCommandHandlerTest extends TestCase
{
    private SettingRepository|MockObject $settingRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settingRepository = $this->createMock(SettingRepository::class);
    }

    public function testUpdateSettingCommandHandlerNothingToUpdate(): void
    {
        $this->settingRepository->expects(self::once())
            ->method('find')
            ->with(321)
            ->willReturn(null);

        $command = new UpdateSettingCommand(
            id: 321,
            name: 'Test Setting',
            value: 'Test Value',
            slug: 'old-slug',
        );

        new UpdateSettingCommandHandler(
            $this->settingRepository,
        )($command);
    }

    public function testUpdateSettingCommandHandler(): void
    {
        $setting = new Setting();
        $setting->setName('Test Setting');
        $setting->setValue('Test Value');
        $setting->setSlug('old-slug');

        $this->settingRepository->expects(self::once())
            ->method('find')
            ->with(321)
            ->willReturn($setting);

        $setting->setName('New Test Tag');
        $setting->setValue('Test Value');
        $setting->setSlug('new-slug');

        $command = new UpdateSettingCommand(
            id: 321,
            name: 'New Test Setting',
            value: 'New Test Value',
            slug: 'new-slug',
        );

        new UpdateSettingCommandHandler(
            $this->settingRepository,
        )($command);
    }
}
