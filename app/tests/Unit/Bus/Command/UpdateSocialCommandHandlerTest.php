<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\UpdateSocialCommand;
use App\Bus\Command\UpdateSocialCommandHandler;
use App\Entity\Social;
use App\Repository\SocialRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateSocialCommandHandlerTest extends TestCase
{
    private SocialRepository|MockObject $socialRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->socialRepository = $this->createMock(SocialRepository::class);
    }

    public function testUpdateSocialCommandHandlerNothingToUpdate(): void
    {
        $this->socialRepository->expects(self::once())
            ->method('find')
            ->with(321)
            ->willReturn(null);

        $command = new UpdateSocialCommand(
            id: 321,
            name: 'Test Social',
            value: 'Test Value',
            uploadImageName: 'test-social.jpg',
            slug: 'old-slug',
        );

        new UpdateSocialCommandHandler(
            $this->socialRepository,
        )($command);
    }

    public function testUpdateSocialCommandHandler(): void
    {
        $social = new Social();
        $social->setName('Test Social');
        $social->setValue('Test Value');
        $social->setUploadImageName('test-social.jpg');
        $social->setSlug('old-slug');

        $this->socialRepository->expects(self::once())
            ->method('find')
            ->with(321)
            ->willReturn($social);

        $social->setName('New Test Social');
        $social->setValue('Test Value');
        $social->setUploadImageName('new-social.jpg');
        $social->setSlug('new-slug');

        $command = new UpdateSocialCommand(
            id: 321,
            name: 'Test Social',
            value: 'Test Value',
            uploadImageName: 'new-social.jpg',
            slug: 'old-slug',
        );

        new UpdateSocialCommandHandler(
            $this->socialRepository,
        )($command);
    }
}
