<?php

declare(strict_types=1);

namespace App\Bus\Command;

use App\Repository\SocialRepository;

class UpdateSocialCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private SocialRepository $socialRepository,
    ) {
    }

    public function __invoke(UpdateSocialCommand $command): void
    {
        $social = $this->socialRepository->find($command->id);
        if (null === $social) {
            return;
        }

        $social->setName($command->name);
        $social->setValue($command->value);
        $social->setUploadImageName($command->uploadImageName);
        $social->setSlug($command->slug);
    }
}
