<?php

declare(strict_types=1);

namespace App\Bus\Query;

use App\Entity\User;
use App\Repository\UserRepository;

class GetUserQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(GetUserQuery $query): ?User
    {
        return $this->userRepository->findOneByEmail($query->email);
    }
}
