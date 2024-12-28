<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AdminCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Admin::class;
    }

    public function getEntityClass(): string
    {
        return self::getEntityFqcn();
    }

    public function getEntityFields(): array
    {
        return ['name', 'email', 'password'];
    }

    /** @phpstan-ignore-next-line  */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var AdminRepository $adminRepository */
        $adminRepository = $entityManager->getRepository(Admin::class);
        $adminExists = $adminRepository->findOneByEmail($entityInstance->getEmail());
        if (null !== $adminExists) {
            $this->addFlash('warning', 'Admin with the specified e-mail address exists');

            return;
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    /** @phpstan-ignore-next-line  */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var AdminRepository $adminRepository */
        $adminRepository = $entityManager->getRepository(Admin::class);
        $adminExists = $adminRepository->findOneByEmail($entityInstance->getEmail());
        if (null !== $adminExists && $entityInstance->getId() !== $adminExists->getId()) {
            $this->addFlash('warning', 'Admin with the specified e-mail address exists');

            return;
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    /** @phpstan-ignore-next-line  */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Admin) {
            return;
        }

        /** @var Admin|null $admin */
        $admin = $this->getUser();
        if ($admin?->getId() === $entityInstance->getId()) {
            $this->addFlash('warning', 'You cannot delete your account');

            return;
        }

        parent::deleteEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            EmailField::new('email'),
            TextField::new('password')
                ->setRequired(true)
                ->onlyWhenCreating()
                ->setFormType(PasswordType::class),
            TextField::new('password')
                ->setRequired(false)
                ->onlyWhenUpdating()
                ->setFormType(PasswordType::class),
            DateTimeField::new('createdAt')
                ->onlyOnIndex(),
            DateTimeField::new('updatedAt')
                ->onlyOnIndex(),
        ];
    }
}
