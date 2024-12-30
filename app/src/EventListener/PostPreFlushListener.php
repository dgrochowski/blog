<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Post;
use App\Repository\FileRepository;
use App\Service\FileService;
use App\Service\RandomStringGenerator;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AsEntityListener(event: Events::preFlush, entity: Post::class)]
class PostPreFlushListener
{
    public function __construct(
        private string $tempDirectory,
        private FileService $fileService,
        private RandomStringGenerator $randomStringGenerator,
        private FileRepository $fileRepository,
    ) {
    }

    public function preFlush(Post $post, PreFlushEventArgs $args): void
    {
        $this->handleFileUpload($post);
    }

    private function handleFileUpload(Post $post): void
    {
        $fileName = $post->getUploadImageName();
        if (null === $fileName) {
            return;
        }

        $uploadedFile = new UploadedFile(
            path: $this->tempDirectory.DIRECTORY_SEPARATOR.$fileName,
            originalName: $fileName,
            test: true,
        );

        $file = $this->fileService->upload(
            $uploadedFile,
            $this->getRandomSlugForFile(),
        );
        $post->setFile($file);
    }

    private function getRandomSlugForFile(): string
    {
        while (true) {
            $slug = $this->randomStringGenerator->generate(10);
            if (null === $this->fileRepository->findOneBy(['slug' => $slug])) {
                return $slug;
            }
        }
    }
}
