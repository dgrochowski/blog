<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Category;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateCategoryCommandHandler
{
    public function __construct(
        private SlugService $slugService,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(CreateCategoryCommand $command): void
    {
        $category = new Category();
        $category->setName($command->getName());
        $category->setSlug($this->slugService->unique($category, $command->getSlug()));

        try {
            $this->entityManager->persist($category);
        } catch (\Throwable $exception) {
            $this->logger->error('CreateCategoryCommandHandler error: '.$exception->getMessage());
        }
    }
}
