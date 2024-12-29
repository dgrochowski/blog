<?php

declare(strict_types=1);

namespace App\Bus\Command;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class CreateFileCommand implements CommandInterface
{
    public function __construct(
        public UploadedFile $uploadedFile,
    ) {
    }
}
