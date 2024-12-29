<?php

declare(strict_types=1);

namespace App\Bus\Query;

use App\Entity\Post;
use App\Repository\PostRepository;

class GetPostQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private PostRepository $postRepository,
    ) {
    }

    public function __invoke(GetPostQuery $query): ?Post
    {
        return $this->postRepository->findOneBySlug($query->slug);
    }
}
