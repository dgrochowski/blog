<?php

declare(strict_types=1);

namespace App\Bus\Query;

use App\Entity\Admin;
use App\Repository\AdminRepository;

class GetAdminQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private AdminRepository $adminRepository,
    ) {
    }

    public function __invoke(GetAdminQuery $query): ?Admin
    {
        return $this->adminRepository->findOneByEmail($query->email);
    }
}
