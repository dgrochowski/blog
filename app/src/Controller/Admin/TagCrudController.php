<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
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

    /** @phpstan-ignore-next-line */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Tag) {
            return;
        }

        $qb = $entityManager->createQueryBuilder();
        $qb->select('COUNT(p.id)')
            ->from(Post::class, 'p')
            ->innerJoin('p.tags', 't')
            ->where('t.slug = :slug')
            ->setParameter('slug', $entityInstance->getSlug())
            ->setMaxResults(1);

        if (0 === $qb->getQuery()->getSingleScalarResult()) {
            parent::deleteEntity($entityManager, $entityInstance);

            return;
        }

        $this->addFlash('warning', 'Object is used');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextField::new('slug')
                ->hideWhenUpdating()
                ->setHelp('value included in post search engine')
                ->setRequired(false),
            TextField::new('slug')
                ->onlyWhenUpdating()
                ->setDisabled()
                ->setHelp('value included in post search engine')
                ->setRequired(true),
            DateTimeField::new('createdAt')
                ->onlyOnIndex(),
            DateTimeField::new('updatedAt')
                ->onlyOnIndex(),
        ];
    }
}
