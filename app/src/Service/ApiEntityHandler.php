<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ApiEntity;

class ApiEntityHandler
{
    /** @phpstan-ignore-next-line */
    public function handleArray(array $value): array
    {
        $result = [];
        $iterator = 0;

        foreach ($value as $item) {
            if ('object' === gettype($item)) {
                $handledObject = $this->handleObject($item);
                if ([] !== $handledObject) {
                    $result[$iterator] = $handledObject;
                    ++$iterator;
                }
            }
        }

        return $result;
    }

    /** @phpstan-ignore-next-line */
    public function handleObject(object $value): array
    {
        $result = [];
        if (in_array(ApiEntity::class, class_implements($value::class), true)) {
            foreach ($value->apiFields() as $field) {
                $getName = 'get'.ucfirst($field);
                $result[$field] = $value->{$getName}();
            }
        }

        return $result;
    }
}
