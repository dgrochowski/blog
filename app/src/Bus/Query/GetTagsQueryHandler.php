<?php

declare(strict_types=1);

namespace App\Bus\Query;

use App\Entity\Tag;
use App\Repository\TagRepository;

class GetTagsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private TagRepository $tagRepository,
    ) {
    }

    /**
     * @return Tag[]
     */
    public function __invoke(GetTagsQuery $query): array
    {
        return $this->tagRepository->findBy(
            criteria: [],
            orderBy: ['name' => 'ASC'],
        );
    }
}
