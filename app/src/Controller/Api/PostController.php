<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Post;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/posts')]
class PostController extends ApiController
{
    protected function getEntityFqcn(): string
    {
        return Post::class;
    }

    /**
     * @return string[]
     */
    protected function availableFilters(): array
    {
        return ['category', 'author'];
    }

    /**
     * @return array<string, string>
     */
    protected function defaultOrder(): array
    {
        return ['publishedAt' => 'DESC'];
    }

    #[Route('/{slug}', methods: ['GET'])]
    public function one(string $slug): JsonResponse
    {
        return $this->oneBySlug($slug);
    }

    #[OA\Parameter(
        name: 'page',
        description: 'Page number',
        in: 'query',
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'Items per page',
        in: 'query',
        schema: new OA\Schema(type: 'integer', example: 10)
    )]
    #[OA\Parameter(
        name: 'search',
        description: 'Search term by name or tags',
        in: 'query',
        schema: new OA\Schema(type: 'string', example: 'My best post')
    )]
    #[OA\Parameter(
        name: 'filter',
        description: 'Filter array by category slugs or author slugs',
        in: 'query',
        schema: new OA\Schema(type: 'string', example: 'filter[category]=slug1,slug2&filter[author]=author_slug')
    )]
    #[Route('/', methods: ['GET'])]
    public function all(Request $request): JsonResponse
    {
        return $this->paginate($request);
    }
}
