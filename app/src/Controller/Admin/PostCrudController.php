<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EDITOR', statusCode: 423)]
class PostCrudController extends AbstractCrudController
{
    private const UPLOADS_PATH = '/public/uploads/temp';

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function getEntityClass(): string
    {
        return self::getEntityFqcn();
    }

    public function getEntityFields(): array
    {
        return [
            'name',
            'publishedAt',
            'description',
            'uploadImageName',
            'tags',
            'category',
            'slug',
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud = parent::configureCrud($crud);

        return $crud->setDefaultSort(['publishedAt' => 'DESC']);
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
        yield DateTimeField::new('publishedAt');
        yield TextField::new('slug')
            ->hideWhenUpdating()
            ->setRequired(false);
        yield TextField::new('slug')
            ->onlyWhenUpdating()
            ->setRequired(true);
        yield AssociationField::new('category');
        yield TextEditorField::new('description')
            ->setLabel('Content')
            ->setNumOfRows(15)
            ->setTrixEditorConfig([
                'blockAttributes' => [
                    'default' => ['tagName' => 'p'],
                    'heading1' => ['tagName' => 'h3'],
                ],
            ])
        ;
        yield AssociationField::new('tags') // Many-to-Many relationship with Tag
            ->hideOnIndex()
            ->setLabel('Tags')
                ->setFormTypeOptions([
                    'by_reference' => false, // Helps with ManyToMany relationships
                ])
                ->setSortable(true);
        yield ArrayField::new('cachedTags', 'Tags')
            ->hideOnForm()
            ->onlyOnIndex();
        yield TextField::new('author')
            ->setDisabled()
            ->hideOnForm()
            ->onlyOnIndex();
        yield DateTimeField::new('createdAt')
            ->onlyOnIndex();
        yield DateTimeField::new('updatedAt')
            ->onlyOnIndex();
    }
}
