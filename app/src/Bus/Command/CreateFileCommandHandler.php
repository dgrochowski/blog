<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Entity\File;
use App\Service\FileService;
use App\Service\RandomStringGenerator;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreateFileCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private SlugService $slugService,
        private EntityManagerInterface $entityManager,
        private RandomStringGenerator $randomStringGenerator,
        private FileService $fileService,
        private string $tempDirectory,
    ) {
    }

    public function __invoke(CreateFileCommand $command): void
    {
        $slug = $command->slug;
        if (null === $slug) {
            $slug = $this->randomStringGenerator->generate(8);
        }
        $uniqueSlug = $this->slugService->unique(File::class, $slug);

        $uploadedFile = new UploadedFile(
            path: $this->tempDirectory.DIRECTORY_SEPARATOR.$command->uploadImageName,
            originalName: $command->uploadImageName,
            test: true,
        );

        $file = $this->fileService->upload($uploadedFile, $uniqueSlug);

        $this->entityManager->persist($file);
    }
}
