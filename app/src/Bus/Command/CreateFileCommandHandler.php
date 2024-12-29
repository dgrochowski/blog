<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;

class CreateFileCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private FileService $fileService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(CreateFileCommand $command): void
    {
        $file = $this->fileService->upload($command->uploadedFile);

        $this->entityManager->persist($file);
    }
}
