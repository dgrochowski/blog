<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdatePostCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private PostRepository $postRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(UpdatePostCommand $command): void
    {
        $post = $this->postRepository->find($command->id);
        if (null === $post) {
            return;
        }

        $post->setName($command->name);
        $post->setDescription($command->description);
        $post->setFile($command->file);
        foreach ($command->tags as $tag) {
            $post->addTag($tag);
        }
        $post->setCategory($command->category);
        $post->setSlug($command->slug);

        $this->entityManager->persist($post);
    }
}
