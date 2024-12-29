<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Bus\Query\GetPostQuery;
use App\Bus\Query\GetPostsQuery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/post')]
class PostController extends ApiController
{
    #[Route('/{slug}', methods: ['GET'])]
    public function one(string $slug): Response
    {
        return $this->jsonResponse(
            $this->bus->query(new GetPostQuery($slug)),
        );
    }

    #[Route('/', methods: ['GET'])]
    public function all(): Response
    {
        return $this->jsonResponse(
            $this->bus->query(new GetPostsQuery()),
        );
    }
}
