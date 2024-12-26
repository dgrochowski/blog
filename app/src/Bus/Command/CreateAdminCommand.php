<?php

declare(strict_types=1);

namespace App\Bus\Command;

final readonly class CreateAdminCommand implements CommandInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public array $roles = ['ROLE_ADMIN'],
    ) {
    }
}
