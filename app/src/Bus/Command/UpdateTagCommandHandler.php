<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Repository\TagRepository;

class UpdateTagCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private TagRepository $tagRepository,
    ) {
    }

    public function __invoke(UpdateTagCommand $command): void
    {
        $tag = $this->tagRepository->find($command->id);
        if (null === $tag) {
            return;
        }

        $tag->setName($command->name);
        $tag->setSlug($command->slug);
    }
}
