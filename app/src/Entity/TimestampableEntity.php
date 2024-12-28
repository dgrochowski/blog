<?php

declare(strict_types=1);

namespace App\Entity;

/** @phpstan-ignore */
interface TimestampableEntity
{
    public function setCreatedAt(\DateTime $createdAt);

    public function getCreatedAt();

    public function setUpdatedAt(\DateTime $updatedAt);

    public function getUpdatedAt();
}
