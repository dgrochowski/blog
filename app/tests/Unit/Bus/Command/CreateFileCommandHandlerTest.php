<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\CreateFileCommand;
use App\Bus\Command\CreateFileCommandHandler;
use App\Entity\File;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class CreateFileCommandHandlerTest extends TestCase
{
    private FileService&MockObject $fileService;
    private EntityManagerInterface&MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileService = $this->createMock(FileService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testCreateFileCommandHandler(): void
    {
        $uploadedFile = $this->createMock(UploadedFile::class);

        $file = new File();
        $file->setIsImage(true);
        $file->setFileName('file.jpg');
        $file->setOriginalName('originalName.jpg');
        $file->setMimeType('image/jpg');
        $file->setSize(1000);

        $this->fileService->expects(self::once())
            ->method('upload')
            ->with($uploadedFile)
            ->willReturn($file);

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($file);

        $command = new CreateFileCommand(
            uploadedFile: $uploadedFile,
        );

        new CreateFileCommandHandler(
            $this->fileService,
            $this->entityManager,
        )($command);
    }
}
