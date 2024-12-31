<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Repository\SettingRepository;

class UpdateSettingCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private SettingRepository $settingRepository,
    ) {
    }

    public function __invoke(UpdateSettingCommand $command): void
    {
        $setting = $this->settingRepository->find($command->id);
        if (null === $setting) {
            return;
        }

        $setting->setName($command->name);
        $setting->setValue($command->value);
        $setting->setSlug($command->slug);
    }
}
