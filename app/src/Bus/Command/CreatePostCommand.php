<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Entity\Category;
use App\Entity\Tag;
use Doctrine\Common\Collections\Collection;

final readonly class CreatePostCommand implements CommandInterface
{
    /**
     * @param Collection<int, Tag> $tags
     */
    public function __construct(
        public string $name,
        public \DateTimeInterface $publishedAt,
        public ?string $description,
        public ?string $uploadImageName,
        public Collection $tags,
        public ?Category $category,
        public ?string $slug = null,
    ) {
    }
}
