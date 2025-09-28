<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @extends AbstractCrudController<User>
 */
#[IsGranted('ROLE_ADMIN', statusCode: 423)]
class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function getEntityClass(): string
    {
        return self::getEntityFqcn();
    }

    public function getEntityFields(): array
    {
        return [
            'name',
            'email',
            'updatedPassword',
            'roles',
            'slug',
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);
        $adminExists = $userRepository->findOneByEmail($entityInstance->getEmail());
        if (null !== $adminExists) {
            $this->addFlash('warning', 'User with the specified e-mail address exists');

            return;
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);
        $adminExists = $userRepository->findOneByEmail($entityInstance->getEmail());
        if (null !== $adminExists && $entityInstance->getId() !== $adminExists->getId()) {
            $this->addFlash('warning', 'User with the specified e-mail address exists');

            return;
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var User|null $admin */
        $admin = $this->getUser();
        if ($admin?->getId() === $entityInstance->getId()) {
            $this->addFlash('warning', 'You cannot delete your account');

            return;
        }

        parent::deleteEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
        yield EmailField::new('email');
        yield TextField::new('updatedPassword', 'Password')
            ->setRequired(true)
            ->onlyWhenCreating()
            ->setFormType(PasswordType::class);
        yield TextField::new('updatedPassword', 'Password')
            ->setRequired(false)
            ->onlyWhenUpdating()
            ->setFormType(PasswordType::class);
        yield ChoiceField::new('roles')
            ->hideOnIndex()
            ->allowMultipleChoices()
            ->renderExpanded()
            ->autocomplete()
            ->setChoices(['Editor' => 'ROLE_EDITOR', 'Admin' => 'ROLE_ADMIN'])
            ->setRequired(true);
        yield TextField::new('slug')
            ->hideWhenUpdating()
            ->setRequired(false);
        yield TextField::new('slug')
            ->onlyWhenUpdating()
            ->setRequired(true);
        yield BooleanField::new('isAdmin')
            ->setDisabled()
            ->onlyOnIndex();
        yield DateTimeField::new('createdAt')
            ->onlyOnIndex();
        yield DateTimeField::new('updatedAt')
            ->onlyOnIndex();
    }
}
