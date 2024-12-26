<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Entity\Category;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;

class CreateCategoryCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private SlugService $slugService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(CreateCategoryCommand $command): void
    {
        $uniqueSlug = $this->slugService->unique(Category::class, $command->slug);

        $category = new Category();
        $category->setName($command->name);
        $category->setSlug($uniqueSlug);

        $this->entityManager->persist($category);
    }
}
