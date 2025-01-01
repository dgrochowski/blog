<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Social;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/socials')]
class SocialController extends ApiController
{
    protected function getEntityFqcn(): string
    {
        return Social::class;
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
    #[Route('/', methods: ['GET'])]
    public function all(Request $request): JsonResponse
    {
        return $this->paginate($request);
    }
}
