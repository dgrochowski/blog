<?php

declare(strict_types=1);

namespace App\Bus\Command;

final readonly class CreateUserCommand implements CommandInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $updatedPassword,
        public array $roles = ['ROLE_USER'],
        public ?string $slug = null,
    ) {
    }
}
