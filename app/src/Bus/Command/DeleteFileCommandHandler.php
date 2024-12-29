<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Repository\FileRepository;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;

class DeleteFileCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private FileRepository $fileRepository,
        private FileService $fileService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(DeleteFileCommand $command): void
    {
        $entity = $this->fileRepository->find($command->id);

        if (null !== $entity) {
            $this->fileService->delete($entity);
            $this->entityManager->remove($entity);
        }
    }
}
