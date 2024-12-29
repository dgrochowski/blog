<?php

declare(strict_types=1);

namespace App\Bus\Query;

use App\Entity\Post;
use App\Repository\PostRepository;

class GetPostsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private PostRepository $postRepository,
    ) {
    }

    /**
     * @return Post[]
     */
    public function __invoke(GetPostsQuery $query): array
    {
        return $this->postRepository->findBy(
            criteria: [],
            orderBy: ['name' => 'ASC'],
        );
    }
}
