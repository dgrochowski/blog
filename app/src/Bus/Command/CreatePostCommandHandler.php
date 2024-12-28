<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Entity\Post;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;

class CreatePostCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private SlugService $slugService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(CreatePostCommand $command): void
    {
        $uniqueSlug = $this->slugService->unique(Post::class, $command->slug);

        $post = new Post();
        $post->setName($command->name);
        $post->setDescription($command->description);
        $post->setImageFilename($command->imageFilename);
        foreach ($command->tags as $tag) {
            $post->addTag($tag);
        }
        $post->setCategory($command->category);
        $post->setSlug($uniqueSlug);

        $this->entityManager->persist($post);
    }
}
