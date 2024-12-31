<?php

namespace App\Controller\Admin;

use App\Entity\Social;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN', statusCode: 423)]
class SocialCrudController extends AbstractCrudController
{
    private const UPLOADS_PATH = '/public/uploads/temp';

    public static function getEntityFqcn(): string
    {
        return Social::class;
    }

    public function getEntityClass(): string
    {
        return self::getEntityFqcn();
    }

    public function getEntityFields(): array
    {
        return ['name', 'value', 'uploadImageName', 'slug'];
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield ImageField::new('filePath')
            ->setLabel('Image')
            ->onlyOnIndex();
        yield TextField::new('filePath', 'Image')
            ->hideOnForm()
            ->onlyWhenUpdating()
            ->setDisabled();
        yield ImageField::new('uploadImageName')
            ->setLabel('Update image')
            ->hideOnIndex()
            ->setRequired(false)
            ->setUploadDir(self::UPLOADS_PATH)
            ->setHelp('Allowed file types: jpg, png, etc.');
        yield TextField::new('name');
        yield TextField::new('value');
        yield TextField::new('slug')
            ->hideWhenUpdating()
            ->setRequired(false);
        yield TextField::new('slug')
            ->onlyWhenUpdating()
            ->setRequired(true);
        yield DateTimeField::new('createdAt')
            ->onlyOnIndex();
        yield DateTimeField::new('updatedAt')
            ->onlyOnIndex();
    }
}
