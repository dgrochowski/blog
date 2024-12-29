<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\DeleteFileCommand;
use App\Bus\Command\DeleteFileCommandHandler;
use App\Entity\File;
use App\Repository\FileRepository;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteFileCommandHandlerTest extends TestCase
{
    private FileRepository&MockObject $fileRepository;
    private FileService&MockObject $fileService;
    private EntityManagerInterface&MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileRepository = $this->createMock(FileRepository::class);
        $this->fileService = $this->createMock(FileService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testDeleteFileCommandHandlerNotRemoveEntity(): void
    {
        $this->fileRepository->expects(self::once())
            ->method('find')
            ->with(123)
            ->willReturn(null);

        $this->entityManager->expects(self::never())
            ->method('remove');

        $command = new DeleteFileCommand(
            id: 123,
        );

        new DeleteFileCommandHandler(
            $this->fileRepository,
            $this->fileService,
            $this->entityManager,
        )($command);
    }

    public function testDeleteFileCommandHandler(): void
    {
        $file = new File();
        $file->setIsImage(true);
        $file->setFileName('file.jpg');
        $file->setOriginalName('originalName.jpg');
        $file->setMimeType('image/jpg');
        $file->setSize(1000);

        $this->fileRepository->expects(self::once())
            ->method('find')
            ->with(123)
            ->willReturn($file);

        $this->entityManager->expects(self::once())
            ->method('remove')
            ->with($file);

        $command = new DeleteFileCommand(
            id: 123,
        );

        new DeleteFileCommandHandler(
            $this->fileRepository,
            $this->fileService,
            $this->entityManager,
        )($command);
    }
}
