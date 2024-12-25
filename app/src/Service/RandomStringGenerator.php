<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\RandomStringGeneratorException;

class RandomStringGenerator
{
    public function generate(int $length): string
    {
        if ($length < 1) {
            throw new RandomStringGeneratorException('Length must be greater than 0');
        }

        $bytes = random_bytes($length);

        return substr(bin2hex($bytes), 0, $length);
    }
}
