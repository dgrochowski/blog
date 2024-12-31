<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\CreateFileCommand;
use App\Bus\Command\CreateFileCommandHandler;
use App\Entity\File;
use App\Service\FileService;
use App\Service\RandomStringGenerator;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

final class CreateFileCommandHandlerTest extends TestCase
{
    private const ASSETS_DIR = 'tests/Unit/assets';

    private SlugService|MockObject $slugService;
    private EntityManagerInterface|MockObject $entityManager;
    private RandomStringGenerator|MockObject $randomStringGenerator;
    private FileService|MockObject $fileService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->slugService = $this->createMock(SlugService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->randomStringGenerator = $this->createMock(RandomStringGenerator::class);
        $this->fileService = $this->createMock(FileService::class);
    }

    public function testCreateFileCommandHandlerWithSlugSuccessfully(): void
    {
        $this->randomStringGenerator->expects(self::never())
            ->method('generate');

        $file = new File();
        $file->setSlug('test-file');

        $this->fileService->expects(self::once())
            ->method('upload')
            ->willReturn($file);

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($file);

        $command = new CreateFileCommand(
            uploadImageName: 'some-random-file.png',
            slug: 'test-file',
        );

        new CreateFileCommandHandler(
            slugService: $this->slugService,
            entityManager: $this->entityManager,
            randomStringGenerator: $this->randomStringGenerator,
            fileService: $this->fileService,
            tempDirectory: self::ASSETS_DIR,
        )($command);
    }

    public function testCreateFileCommandHandlerWithoutSlugSuccessfully(): void
    {
        $this->randomStringGenerator->expects(self::once())
            ->method('generate')
            ->willReturn('generated-slug');

        $file = new File();
        $file->setSlug('generated-slug');

        $this->fileService->expects(self::once())
            ->method('upload')
            ->willReturn($file);

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($file);

        $command = new CreateFileCommand(
            uploadImageName: 'some-random-file.png',
            slug: null,
        );

        new CreateFileCommandHandler(
            slugService: $this->slugService,
            entityManager: $this->entityManager,
            randomStringGenerator: $this->randomStringGenerator,
            fileService: $this->fileService,
            tempDirectory: self::ASSETS_DIR,
        )($command);
    }

    public function testCreateFileCommandHandlerThrowsExceptionWhenFileNotFound(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('The file "tests/Unit/assets/not-existing-file" does not exist');

        $this->randomStringGenerator->expects(self::once())
            ->method('generate')
            ->willReturn('generated-slug');

        $file = new File();
        $file->setSlug('generated-slug');

        $this->fileService->expects(self::never())
            ->method('upload');

        $this->entityManager->expects(self::never())
            ->method('persist');

        $command = new CreateFileCommand(
            uploadImageName: 'not-existing-file',
            slug: null,
        );

        new CreateFileCommandHandler(
            slugService: $this->slugService,
            entityManager: $this->entityManager,
            randomStringGenerator: $this->randomStringGenerator,
            fileService: $this->fileService,
            tempDirectory: self::ASSETS_DIR,
        )($command);
    }
}
