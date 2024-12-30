<?php

declare(strict_types=1);

namespace App\Bus\Query;

use App\Entity\File;
use App\Repository\FileRepository;

class GetFileQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private FileRepository $fileRepository,
    ) {
    }

    public function __invoke(GetFileQuery $query): ?File
    {
        return $this->fileRepository->findOneBySlug($query->slug);
    }
}
