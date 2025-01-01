<?php

namespace App\Controller\Admin;

use App\Bus\Bus;
use App\Bus\Command\DeleteCommand;
use App\Bus\Query\GetByIdQuery;
use App\Service\ControllerUtils;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController as EasyCrudController;

abstract class AbstractCrudController extends EasyCrudController
{
    public function __construct(
        protected Bus $bus,
        protected EntityManagerInterface $entityManager,
        protected ControllerUtils $controllerUtils,
    ) {
    }

    abstract public function getEntityClass(): string;

    /**
     * @return string[]
     */
    public function getEntityFields(): array
    {
        return ['name'];
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud = parent::configureCrud($crud);

        return $crud
            ->showEntityActionsInlined()
            ->overrideTemplate(
                templateName: 'crud/edit',
                templatePath: 'admin/crud/edit.html.twig',
            )
            ->setDefaultSort(['createdAt' => 'DESC'])
        ;
    }

    /** @phpstan-ignore-next-line */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $className = $this->getEntityClass();
        if (!$entityInstance instanceof $className) {
            return;
        }

        $this->bus->command(
            $this->controllerUtils->buildCommand('Create', $entityInstance, $this->getEntityFields()),
        );
        $this->addFlash('success', 'Added successfully');
    }

    /** @phpstan-ignore-next-line */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $className = $this->getEntityClass();
        if (!$entityInstance instanceof $className) {
            return;
        }

        if ($this->controllerUtils->isSluggable($this->getEntityFields(), $className)
            && $this->controllerUtils->isSlugTaken($entityInstance)) {
            $this->addFlash('warning', 'Slug already taken');

            return;
        }

        $fields = array_merge(
            ['id'],
            $this->getEntityFields(),
        );
        $this->bus->command(
            $this->controllerUtils->buildCommand('Update', $entityInstance, $fields),
        );
        $this->addFlash('success', 'Updated successfully');
    }

    /** @phpstan-ignore-next-line */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $className = $this->getEntityClass();
        if (!$entityInstance instanceof $className) {
            return;
        }

        $id = $entityInstance->getId();
        $this->bus->command(new DeleteCommand($className, $id));
        if (null === $this->bus->query(new GetByIdQuery(className: $className, id: $id))) {
            $this->addFlash('success', 'Removed successfully');

            return;
        }

        $this->addFlash('warning', 'It failed to remove');
    }
}
