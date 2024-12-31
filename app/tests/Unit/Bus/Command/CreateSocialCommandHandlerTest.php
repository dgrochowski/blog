<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\CreateSocialCommand;
use App\Bus\Command\CreateSocialCommandHandler;
use App\Entity\Social;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateSocialCommandHandlerTest extends TestCase
{
    private SlugService|MockObject $slugService;
    private EntityManagerInterface|MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->slugService = $this->createMock(SlugService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testCreateSocialCommandHandler(): void
    {
        $this->slugService->expects(self::once())
            ->method('unique')
            ->willReturn('test-social');

        $social = new Social();
        $social->setName('Test Social');
        $social->setValue('Test Value');
        $social->setSlug('test-social');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($social);

        $command = new CreateSocialCommand(
            name: 'Test Social',
            value: 'Test Value',
            slug: 'test-social',
        );

        new CreateSocialCommandHandler(
            slugService: $this->slugService,
            entityManager: $this->entityManager,
        )($command);
    }

    public function testCreateSocialCommandHandlerThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('unable to persist');

        $this->slugService->expects(self::once())
            ->method('unique')
            ->willReturn('test-social');

        $social = new Social();
        $social->setName('Test Social');
        $social->setValue('Test Value');
        $social->setSlug('test-social');

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->willThrowException(new \Exception('unable to persist'));

        $command = new CreateSocialCommand(
            name: 'Test Social',
            value: 'Test Value',
            slug: 'test-social',
        );

        new CreateSocialCommandHandler(
            slugService: $this->slugService,
            entityManager: $this->entityManager,
        )($command);
    }
}
