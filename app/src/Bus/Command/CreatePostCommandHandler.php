<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;

class CreatePostCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private SlugService $slugService,
        private EntityManagerInterface $entityManager,
        private CategoryRepository $categoryRepository,
        private TagRepository $tagRepository,
    ) {
    }

    public function __invoke(CreatePostCommand $command): void
    {
        $uniqueSlug = $this->slugService->unique(Post::class, $command->slug ?? $command->name);

        $post = new Post();
        $post->setName($command->name);
        $post->setDescription($command->description);
        $post->setUploadImageName($command->uploadImageName);
        foreach ($command->tags->toArray() as $lazyTag) {
            $tag = $this->tagRepository->find($lazyTag->getId());
            $post->addTag($tag);
        }

        $categoryId = $command->category?->getId();
        $category = null;
        if (null !== $categoryId) {
            $category = $this->categoryRepository->find($categoryId);
            $post->setCategory($category);
        }

        $post->setSlug($uniqueSlug);

        $this->entityManager->persist($post);
        $category?->addPost($post);
    }
}
