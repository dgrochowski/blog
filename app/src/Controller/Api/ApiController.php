<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Bus\Bus;
use App\Bus\Query\GetBySlugQuery;
use App\Bus\Query\GetCountQuery;
use App\Bus\Query\GetPaginateQuery;
use App\DTO\PaginationDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

abstract class ApiController extends AbstractController
{
    public function __construct(
        protected Bus $bus,
        protected SerializerInterface $serializer,
    ) {
    }

    protected function getEntityFqcn(): string
    {
        throw new \BadMethodCallException('Not implemented');
    }

    protected function defaultPerPageLimit(): int
    {
        return 10;
    }

    protected function oneBySlug(string $slug): JsonResponse
    {
        $data = $this->bus->query(new GetBySlugQuery(
            className: $this->getEntityFqcn(),
            slug: $slug,
        ));

        if (null !== $data) {
            return $this->json(
                data: ['data' => $data],
                context: ['groups' => 'api'],
            );
        }

        return $this->json(
            data: ['error' => 'Not found'],
            status: 404,
        );
    }

    protected function paginate(Request $request): JsonResponse
    {
        $className = $this->getEntityFqcn();
        $page = $this->getPage($request);
        $limit = $this->getLimit($request);

        $count = $this->bus->query(new GetCountQuery(
            className: $className,
        ));
        $data = $this->bus->query(new GetPaginateQuery(
            className: $className,
            page: $page,
            limit: $limit,
        ));

        $dto = new PaginationDTO(
            page: $page,
            totalPages: (int) ceil($count / $limit),
            limit: $limit,
            data: $data,
        );

        return $this->json(
            data: $dto,
            context: [
                'groups' => 'api',
                'enable_max_depth' => true,
            ],
        );
    }

    protected function getPage(Request $request): int
    {
        $page = (int) $request->query->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        return $page;
    }

    protected function getLimit(Request $request): int
    {
        $limit = (int) $request->query->get('limit', $this->defaultPerPageLimit());
        if ($limit < 1) {
            $limit = 1;
        }

        return $limit;
    }
}
