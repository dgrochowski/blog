<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Entity\Category;
use App\Entity\File;
use App\Entity\Tag;

final readonly class CreatePostCommand implements CommandInterface
{
    /**
     * @param Tag[] $tags
     */
    public function __construct(
        public string $name,
        public ?string $description,
        public ?File $file,
        public array $tags,
        public ?Category $category,
        public ?string $slug = null,
    ) {
    }
}
