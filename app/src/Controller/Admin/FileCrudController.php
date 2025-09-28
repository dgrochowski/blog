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

/**
 * @extends AbstractCrudController<File>
 */
#[IsGranted('ROLE_ADMIN', statusCode: 423)]
class FileCrudController extends AbstractCrudController
{
    private const UPLOADS_PATH = '/public/uploads/temp';

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
            ->disable(Action::EDIT)
        ;
    }

    public function getEntityFields(): array
    {
        return [
            'uploadImageName',
            'slug',
        ];
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();
        yield ImageField::new('filePath')
            ->setLabel('Image')
            ->onlyOnIndex();
        yield ImageField::new('uploadImageName')
            ->setLabel('Update image')
            ->hideOnIndex()
            ->setRequired(false)
            ->setUploadDir(self::UPLOADS_PATH)
            ->setHelp('Allowed file types: jpg, png, etc.');
        yield BooleanField::new('isImage')
            ->setDisabled()
            ->onlyOnIndex();
        yield TextField::new('fileName', 'Name')
            ->onlyOnIndex();
        yield TextField::new('originalName')
            ->onlyOnIndex();
        yield NumberField::new('size', 'Size [MB]')
            ->onlyOnIndex();
        yield TextField::new('directory')
            ->onlyOnIndex();
        yield TextField::new('slug')
            ->setRequired(false);
        yield DateTimeField::new('createdAt')
            ->onlyOnIndex();
        yield DateTimeField::new('updatedAt')
            ->onlyOnIndex();
    }
}
