<?php

declare(strict_types=1);

namespace App\Bus\Query;

use App\Entity\Setting;
use App\Repository\SettingRepository;

class GetSettingsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private SettingRepository $settingRepository,
    ) {
    }

    /**
     * @return Setting[]
     */
    public function __invoke(GetSettingsQuery $query): array
    {
        return $this->settingRepository->findBy(
            criteria: [],
            orderBy: ['name' => 'ASC'],
        );
    }
}
