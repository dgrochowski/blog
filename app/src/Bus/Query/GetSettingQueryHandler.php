<?php

declare(strict_types=1);

namespace App\Bus\Query;

use App\Entity\Setting;
use App\Repository\SettingRepository;

class GetSettingQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private SettingRepository $settingRepository,
    ) {
    }

    public function __invoke(GetSettingQuery $query): ?Setting
    {
        return $this->settingRepository->findOneBySlug($query->slug);
    }
}
