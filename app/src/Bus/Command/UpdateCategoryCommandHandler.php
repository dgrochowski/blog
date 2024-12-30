<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Repository\CategoryRepository;

class UpdateCategoryCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {
    }

    public function __invoke(UpdateCategoryCommand $command): void
    {
        $category = $this->categoryRepository->find($command->id);
        if (null === $category) {
            return;
        }

        $category->setName($command->name);
        $category->setSlug($command->slug);
    }
}
