<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\FileRepository;
use App\Service\FileService;
use App\Service\RandomStringGenerator;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AsEntityListener(event: Events::preFlush, entity: Post::class)]
class PostPreFlushListener
{
    public function __construct(
        private string $tempDirectory,
        private FileService $fileService,
        private RandomStringGenerator $randomStringGenerator,
        private FileRepository $fileRepository,
        private Security $security,
    ) {
    }

    public function preFlush(Post $post, PreFlushEventArgs $args): void
    {
        $this->handleFileUpload($post);
        $this->assignAuthor($post);
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

    private function assignAuthor(Post $post): void
    {
        /** @var User $author */
        $author = $this->security->getUser();
        $post->setAuthor($author);
    }
}
