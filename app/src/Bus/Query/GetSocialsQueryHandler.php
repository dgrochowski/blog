<?php

declare(strict_types=1);

namespace App\Bus\Query;

use App\Entity\Social;
use App\Repository\SocialRepository;

class GetSocialsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private SocialRepository $socialRepository,
    ) {
    }

    /**
     * @return Social[]
     */
    public function __invoke(GetSocialsQuery $query): array
    {
        return $this->socialRepository->findBy(
            criteria: [],
            orderBy: ['name' => 'ASC'],
        );
    }
}
