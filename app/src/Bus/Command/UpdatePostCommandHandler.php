<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Repository\TagRepository;

class UpdatePostCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private PostRepository $postRepository,
        private CategoryRepository $categoryRepository,
        private TagRepository $tagRepository,
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
        $post->setUploadImageName($command->uploadImageName);
        foreach ($command->tags->toArray() as $lazyTag) {
            $tag = $this->tagRepository->find($lazyTag->getId());
            $post->addTag($tag);
        }

        $categoryId = $command->category?->getId();
        if (null !== $categoryId) {
            $category = $this->categoryRepository->find($categoryId);
            $post->setCategory($category);
        }

        $post->setSlug($command->slug);
    }
}
