<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Query\GetCategoriesQuery;
use App\Query\GetCategoryQuery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category')]
class CategoryController extends ApiController
{
    #[Route('/{slug}', methods: ['GET'])]
    public function one(string $slug): Response
    {
        return $this->jsonResponse(
            $this->bus->query(new GetCategoryQuery($slug)),
        );
    }

    #[Route('/', methods: ['GET'])]
    public function all(): Response
    {
        return $this->jsonResponse(
            $this->bus->query(new GetCategoriesQuery()),
        );
    }
}
