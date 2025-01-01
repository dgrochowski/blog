<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Tag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tags')]
class TagController extends ApiController
{
    protected function getEntityFqcn(): string
    {
        return Tag::class;
    }

    #[Route('/{slug}', methods: ['GET'])]
    public function one(string $slug): JsonResponse
    {
        return $this->oneBySlug($slug);
    }

    #[Route('/', methods: ['GET'])]
    public function all(Request $request): JsonResponse
    {
        return $this->paginate($request);
    }
}
