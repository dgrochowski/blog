<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\SlugEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class SlugService
{
    public function __construct(
        private SluggerInterface $slugger,
        private RandomStringGenerator $randomStringGenerator,
        private EntityManagerInterface $entityManager,
        private int $randomSlugLength = 8,
    ) {
    }

    public function generateFromString(string $value): string
    {
        return (string) $this->slugger->slug($value)->lower();
    }

    public function generateRandom(?int $length = null): string
    {
        if (null === $length) {
            $length = $this->randomSlugLength;
        }

        $randomString = $this->randomStringGenerator->generate($length);

        return $this->generateFromString($randomString);
    }

    public function unique(SlugEntity $entity, ?string $value = null): string
    {
        if (null === $value) {
            $slug = $this->generateRandom($this->randomSlugLength);
        } else {
            $slug = $this->generateFromString($value);
        }

        $i = 1;
        $originalSlug = $slug;
        while (true) {
            if (null === $this->entityManager->find($entity::class, ['slug' => $slug])) {
                break;
            } else {
                $slug = $originalSlug.'-'.$i;
                ++$i;
            }
        }

        return $slug;
    }
}
