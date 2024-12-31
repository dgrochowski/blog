<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Entity\Social;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;

class CreateSocialCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private SlugService $slugService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(CreateSocialCommand $command): void
    {
        $uniqueSlug = $this->slugService->unique(Social::class, $command->slug ?? $command->name);

        $social = new Social();
        $social->setName($command->name);
        $social->setValue($command->value);
        $social->setSlug($uniqueSlug);

        $this->entityManager->persist($social);
    }
}
