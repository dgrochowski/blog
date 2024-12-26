<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminCrudController extends AbstractCrudController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Admin::class;
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
        if ($entityInstance instanceof Admin && null !== $entityInstance->getUpdatePassword()) {
            $this->updatePassword($entityInstance);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    /** @phpstan-ignore-next-line  */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Admin && null !== $entityInstance->getUpdatePassword()) {
            $this->updatePassword($entityInstance);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            EmailField::new('email'),
            TextField::new('updatePassword', 'Password')
                ->setRequired(true)
                ->onlyWhenCreating()
                ->setFormType(PasswordType::class),
            TextField::new('updatePassword', 'Password')
                ->setRequired(false)
                ->onlyWhenUpdating()
                ->setFormType(PasswordType::class),
        ];
    }

    private function updatePassword(Admin $admin): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            $admin->getUpdatePassword(),
        );

        $admin->setPassword($hashedPassword);
    }
}
