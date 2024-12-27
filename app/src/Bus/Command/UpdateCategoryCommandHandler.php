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
        $category = $this->categoryRepository->findOneBySlug($command->oldSlug);
        if (null === $category) {
            return;
        }

        $category->setName($command->name);

        if (null !== $command->newSlug) {
            $newSlugCategory = $this->categoryRepository->findOneBySlug($command->newSlug);
            if (null === $newSlugCategory) {
                $category->setSlug($command->newSlug);
            }
        }

        $this->entityManager->persist($category);
    }
}
