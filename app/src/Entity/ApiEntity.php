<?php

declare(strict_types=1);

namespace App\Entity;

interface ApiEntity
{
    /**
     * @return string[]
     */
    public function apiFields(): array;
}
