<?php

namespace App\Controller\Admin;

use App\Entity\File;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN', statusCode: 423)]
class FileCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return File::class;
    }

    public function getEntityClass(): string
    {
        return self::getEntityFqcn();
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        return $actions
            ->disable(Action::EDIT, Action::NEW)
        ;
    }

    public function getEntityFields(): array
    {
        return [
            'isImage',
            'fileName',
            'originalName',
            'size',
            'directory',
            'slug',
        ];
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield ImageField::new('filePath')
                ->setLabel('Image');
        yield BooleanField::new('isImage')
            ->setDisabled();
        yield TextField::new('fileName', 'Name');
        yield TextField::new('originalName');
        yield NumberField::new('size', 'Size [MB]');
        yield TextField::new('directory');
        yield TextField::new('slug');
        yield DateTimeField::new('createdAt')
                ->onlyOnIndex();
        yield DateTimeField::new('updatedAt')
                ->onlyOnIndex();
    }
}
