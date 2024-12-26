<?php

declare(strict_types=1);

namespace App\Bus\Query;

use App\Entity\Category;
use App\Repository\CategoryRepository;

class GetCategoryQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {
    }

    public function __invoke(GetCategoryQuery $query): ?Category
    {
        return $this->categoryRepository->findOneBySlug($query->slug);
    }
}
