<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Bus\Bus;
use App\Bus\Query\GetBySlugQuery;
use App\Bus\Query\GetCountQuery;
use App\Bus\Query\GetPaginateQuery;
use App\DTO\PaginationDTO;
use App\Entity\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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

    /**
     * @return string[]
     */
    protected function availableFilters(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    protected function defaultOrder(): array
    {
        return ['name' => 'ASC'];
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
        return $this->json(
            data: $this->getPaginationDTO($request),
            context: $this->getPaginateContext($request),
        );
    }

    protected function getPage(Request $request): int
    {
        try {
            $page = (int) $request->query->get('page', 1);
        } catch (BadRequestException $e) {
            return 1;
        }

        if ($page < 1) {
            $page = 1;
        }

        return $page;
    }

    protected function getLimit(Request $request): int
    {
        try {
            $limit = (int) $request->query->get('limit', $this->defaultPerPageLimit());
        } catch (BadRequestException $e) {
            return $this->defaultPerPageLimit();
        }

        if ($limit < 1) {
            $limit = 1;
        }

        if ($limit > 50) {
            $limit = 50;
        }

        return $limit;
    }

    protected function getCount(Request $request): int
    {
        return $this->bus->query(new GetCountQuery(
            className: $this->getEntityFqcn(),
            filter: $this->getFilters($request),
            search: $this->getSearch($request),
        ));
    }

    /**
     * @return Entity[]
     */
    protected function getPaginateData(Request $request): array
    {
        return $this->bus->query(new GetPaginateQuery(
            className: $this->getEntityFqcn(),
            page: $this->getPage($request),
            limit: $this->getLimit($request),
            filter: $this->getFilters($request),
            search: $this->getSearch($request),
            order: $this->defaultOrder(),
        ));
    }

    protected function getPaginationDTO(Request $request): PaginationDTO
    {
        $page = $this->getPage($request);
        $limit = $this->getLimit($request);
        $count = $this->getCount($request);
        $data = $this->getPaginateData($request);

        return new PaginationDTO(
            page: $page,
            totalPages: (int) ceil($count / $limit),
            limit: $limit,
            data: $data,
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function getPaginateContext(Request $request): array
    {
        return [
            'groups' => 'api',
            'enable_max_depth' => true,
        ];
    }

    protected function sanitizeValue(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * @return array<string, string[]>
     */
    protected function getFilters(Request $request): array
    {
        try {
            $filterQuery = $request->query->all('filter');
        } catch (BadRequestException $e) {
            return [];
        }

        $filters = [];
        foreach ($filterQuery as $filter => $value) {
            if (in_array($filter, $this->availableFilters(), true)) {
                $filters[$filter] = explode(',', $this->sanitizeValue($value));
            }
        }

        return $filters;
    }

    protected function getSearch(Request $request): ?string
    {
        $search = $request->query->get('search');
        if (in_array(gettype($search), ['string', 'null'], true)) {
            return $search;
        }

        return null;
    }
}
