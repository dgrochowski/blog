<?php

declare(strict_types=1);

namespace App\Service;

use App\Bus\Command\CommandInterface;
use App\Entity\SlugEntity;
use App\Exception\ControllerUtilsException;
use Doctrine\ORM\EntityManagerInterface;

class ControllerUtils
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /** @phpstan-ignore-next-line */
    public function isSlugTaken($entityInstance): bool
    {
        /** @phpstan-ignore-next-line */
        $repository = $this->entityManager->getRepository($entityInstance::class);
        $entity = $repository->findOneBy(['slug' => $entityInstance->getSlug()]);

        return null !== $entity && $entity->getId() !== $entityInstance?->getId();
    }

    /**
     * @param string[] $fields
     */
    public function isSluggable(array $fields, string $class): bool
    {
        return in_array('slug', $fields, true)
            && in_array(SlugEntity::class, class_implements($class), true);
    }

    /** @phpstan-ignore-next-line */
    public function buildCommand(string $prefix, $entityInstance, array $fields): CommandInterface
    {
        $class = sprintf(
            'App\Bus\Command\%s%sCommand',
            $prefix,
            $this->getClassName($entityInstance::class),
        );
        if (false === class_exists($class)) {
            throw new ControllerUtilsException(sprintf('Class "%s" not found', $class));
        }

        $arguments = [];
        foreach ($fields as $field) {
            $getter = 'get'.ucfirst($field);

            if (false === method_exists($entityInstance, $getter)) {
                throw new ControllerUtilsException(sprintf('Method "%s" not found in class "%s"', $getter, $entityInstance::class));
            }

            $arguments[$field] = $entityInstance->$getter();
        }
        $serializedArguments = serialize($arguments);

        return new $class(
            ...array_values(unserialize($serializedArguments))
        );
    }

    private function getClassName(string $className): string
    {
        $explodedNamespace = explode('\\', $className);

        return end($explodedNamespace);
    }
}
