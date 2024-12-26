<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Bus\Query\GetTagQuery;
use App\Bus\Query\GetTagsQuery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tag')]
class TagController extends ApiController
{
    #[Route('/{slug}', methods: ['GET'])]
    public function one(string $slug): Response
    {
        return $this->jsonResponse(
            $this->bus->query(new GetTagQuery($slug)),
        );
    }

    #[Route('/', methods: ['GET'])]
    public function all(): Response
    {
        return $this->jsonResponse(
            $this->bus->query(new GetTagsQuery()),
        );
    }
}
