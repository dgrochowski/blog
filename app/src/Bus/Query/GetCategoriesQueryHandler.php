<?php

declare(strict_types=1);

namespace App\Bus\Query;

use App\Entity\Category;
use App\Repository\CategoryRepository;

class GetCategoriesQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {
    }

    /**
     * @return Category[]
     */
    public function __invoke(GetCategoriesQuery $query): array
    {
        return $this->categoryRepository->findBy(
            criteria: [],
            orderBy: ['name' => 'ASC'],
        );
    }
}
