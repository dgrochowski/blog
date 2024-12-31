<?php

declare(strict_types=1);

namespace App\Bus\Query;

use App\Entity\Social;
use App\Repository\SocialRepository;

class GetSocialQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private SocialRepository $socialRepository,
    ) {
    }

    public function __invoke(GetSocialQuery $query): ?Social
    {
        return $this->socialRepository->findOneBySlug($query->slug);
    }
}
