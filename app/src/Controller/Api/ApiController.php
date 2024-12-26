<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Bus\Bus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class ApiController extends AbstractController
{
    public function __construct(
        protected Bus $bus,
    ) {
    }

    protected function jsonResponse(mixed $value): JsonResponse
    {
        if (null === $value) {
            return $this->json(
                ['error' => 'Not found'],
                404,
            );
        }

        return $this->json($value);
    }
}
