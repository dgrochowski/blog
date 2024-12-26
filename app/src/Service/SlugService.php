<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\SlugEntity;
use App\Exception\SlugServiceException;
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

    public function unique(string $className, ?string $value = null): string
    {
        if (false === class_exists($className)) {
            throw new SlugServiceException("Class $className does not exist");
        }

        if (false === in_array(SlugEntity::class, class_implements($className))) {
            throw new SlugServiceException("Class $className does not implement SlugEntity");
        }

        if (null === $value) {
            $slug = $this->generateRandom($this->randomSlugLength);
        } else {
            $slug = $this->generateFromString($value);
        }

        $i = 1;
        $originalSlug = $slug;
        $repository = $this->entityManager->getRepository($className);
        while (true) {
            if (null === $repository->findOneBy(['slug' => $slug])) {
                break;
            } else {
                $slug = $originalSlug.'-'.$i;
                ++$i;
            }
        }

        return $slug;
    }
}
