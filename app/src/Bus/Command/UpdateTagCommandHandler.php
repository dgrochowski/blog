<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdateTagCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private TagRepository $tagRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(UpdateTagCommand $command): void
    {
        $tag = $this->tagRepository->find($command->id);
        if (null === $tag) {
            return;
        }

        $tag->setName($command->name);
        $tag->setSlug($command->slug);

        $this->entityManager->persist($tag);
    }
}
