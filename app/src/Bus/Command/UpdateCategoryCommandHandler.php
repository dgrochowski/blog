<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdateCategoryCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private EntityManagerInterface $entityManager,
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

        $this->entityManager->persist($category);
    }
}
