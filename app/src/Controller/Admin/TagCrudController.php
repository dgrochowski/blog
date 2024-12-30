<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EDITOR', statusCode: 423)]
class TagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tag::class;
    }

    public function getEntityClass(): string
    {
        return self::getEntityFqcn();
    }

    public function getEntityFields(): array
    {
        return ['name', 'slug'];
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
                ->setRequired(true),
            DateTimeField::new('createdAt')
                ->onlyOnIndex(),
            DateTimeField::new('updatedAt')
                ->onlyOnIndex(),
        ];
    }
}
