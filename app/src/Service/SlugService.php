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
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function generateFromString(string $value): string
    {
        return (string) $this->slugger->slug($value)->lower();
    }

    public function unique(string $className, string $value): string
    {
        if (false === class_exists($className)) {
            throw new SlugServiceException("Class $className does not exist");
        }

        if (false === in_array(SlugEntity::class, class_implements($className), true)) {
            throw new SlugServiceException("Class $className does not implement SlugEntity");
        }

        $slug = $this->generateFromString($value);

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
