<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Entity\Setting;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;

class CreateSettingCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private SlugService $slugService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(CreateSettingCommand $command): void
    {
        $uniqueSlug = $this->slugService->unique(Setting::class, $command->slug ?? $command->name);

        $setting = new Setting();
        $setting->setName($command->name);
        $setting->setValue($command->value);
        $setting->setSlug($uniqueSlug);

        $this->entityManager->persist($setting);
    }
}
