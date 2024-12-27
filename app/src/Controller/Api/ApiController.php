<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Bus\Bus;
use App\Service\ApiEntityHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class ApiController extends AbstractController
{
    public function __construct(
        protected Bus $bus,
        protected ApiEntityHandler $apiEntityHandler,
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

        switch (gettype($value)) {
            case 'array':
                return $this->json($this->apiEntityHandler->handleArray($value));
            case 'object':
                return $this->json($this->apiEntityHandler->handleObject($value));
            default:
                return $this->json(
                    ['error' => 'Invalid value'],
                    400,
                );
        }
    }
}
