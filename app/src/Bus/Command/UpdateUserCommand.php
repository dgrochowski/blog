<?php

declare(strict_types=1);

namespace App\Bus\Command;

final readonly class UpdateUserCommand implements CommandInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $updatedPassword,
        public array $roles,
        public string $slug,
    ) {
    }
}
