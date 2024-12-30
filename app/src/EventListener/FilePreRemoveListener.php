<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\File;
use App\Service\FileService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preRemove, entity: File::class)]
class FilePreRemoveListener
{
    public function __construct(
        private FileService $fileService,
    ) {
    }

    public function preRemove(File $file, PreRemoveEventArgs $args): void
    {
        $this->fileService->delete($file);
    }
}
