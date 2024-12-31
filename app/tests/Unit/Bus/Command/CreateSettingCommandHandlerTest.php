<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\CreateSettingCommand;
use App\Bus\Command\CreateSettingCommandHandler;
use App\Entity\Setting;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateSettingCommandHandlerTest extends TestCase
{
    private SlugService|MockObject $slugService;
    private EntityManagerInterface|MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->slugService = $this->createMock(SlugService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testCreateSettingCommandHandler(): void
    {
        $this->slugService->expects(self::once())
            ->method('unique')
            ->willReturn('test-setting');

        $setting = new Setting();
        $setting->setName('Test Setting');
        $setting->setValue('Test Value');
        $setting->setSlug('test-setting');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($setting);

        $command = new CreateSettingCommand(
            name: 'Test Setting',
            value: 'Test Value',
            slug: 'test-setting',
        );

        new CreateSettingCommandHandler(
            slugService: $this->slugService,
            entityManager: $this->entityManager,
        )($command);
    }

    public function testCreateSettingCommandHandlerThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('unable to persist');

        $this->slugService->expects(self::once())
            ->method('unique')
            ->willReturn('test-setting');

        $setting = new Setting();
        $setting->setName('Test Setting');
        $setting->setValue('Test Value');
        $setting->setSlug('test-setting');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->willThrowException(new \Exception('unable to persist'));

        $command = new CreateSettingCommand(
            name: 'Test Setting',
            value: 'Test Value',
            slug: 'test-setting',
        );

        new CreateSettingCommandHandler(
            slugService: $this->slugService,
            entityManager: $this->entityManager,
        )($command);
    }
}
