<?php

declare(strict_types=1);

namespace App\Bus\Query;

use App\Entity\Tag;
use App\Repository\TagRepository;

class GetTagQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private TagRepository $tagRepository,
    ) {
    }

    public function __invoke(GetTagQuery $query): ?Tag
    {
        return $this->tagRepository->findOneBySlug($query->slug);
    }
}
