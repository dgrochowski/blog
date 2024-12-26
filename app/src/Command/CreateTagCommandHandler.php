<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Tag;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;

class CreateTagCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private SlugService $slugService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(CreateTagCommand $command): void
    {
        $uniqueSlug = $this->slugService->unique(Tag::class, $command->slug);

        $tag = new Tag();
        $tag->setName($command->name);
        $tag->setSlug($uniqueSlug);

        $this->entityManager->persist($tag);
    }
}
