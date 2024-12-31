<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Bus\Query\GetSocialQuery;
use App\Bus\Query\GetSocialsQuery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/social')]
class SocialController extends ApiController
{
    #[Route('/{slug}', methods: ['GET'])]
    public function one(string $slug): Response
    {
        return $this->jsonResponse(
            $this->bus->query(new GetSocialQuery($slug)),
        );
    }

    #[Route('/', methods: ['GET'])]
    public function all(): Response
    {
        return $this->jsonResponse(
            $this->bus->query(new GetSocialsQuery()),
        );
    }
}
