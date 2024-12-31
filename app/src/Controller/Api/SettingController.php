<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Bus\Query\GetSettingQuery;
use App\Bus\Query\GetSettingsQuery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/setting')]
class SettingController extends ApiController
{
    #[Route('/{slug}', methods: ['GET'])]
    public function one(string $slug): Response
    {
        return $this->jsonResponse(
            $this->bus->query(new GetSettingQuery($slug)),
        );
    }

    #[Route('/', methods: ['GET'])]
    public function all(): Response
    {
        return $this->jsonResponse(
            $this->bus->query(new GetSettingsQuery()),
        );
    }
}
