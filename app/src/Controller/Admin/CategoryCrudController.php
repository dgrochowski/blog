<?php

namespace App\Controller\Admin;

use App\Bus\Bus;
use App\Bus\Command\CreateCategoryCommand;
use App\Bus\Command\UpdateCategoryCommand;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public function __construct(
        private Bus $bus,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setDefaultSort(['name' => 'ASC'])
        ;
    }

    /** @phpstan-ignore-next-line  */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Category) {
            $this->bus->command(new UpdateCategoryCommand(
                name: $entityInstance->getName(),
                oldSlug: $entityInstance->getSlug(),
                newSlug: $entityInstance->getNewSlug(),
            ));
        }
    }

    /** @phpstan-ignore-next-line  */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Category) {
            $this->bus->command(new CreateCategoryCommand(
                name: $entityInstance->getName(),
                slug: $entityInstance->getSlug(),
            ));
        }
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextField::new('slug')
                ->hideWhenUpdating()
                ->setRequired(false),
            TextField::new('slug')
                ->onlyWhenUpdating()
                ->setDisabled()
                ->setRequired(false),
            TextField::new('newSlug', 'New slug')
                ->onlyWhenUpdating()
                ->setRequired(false),
        ];
    }
}
