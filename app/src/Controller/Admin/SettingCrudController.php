<?php

namespace App\Controller\Admin;

use App\Bus\Bus;
use App\Entity\Setting;
use App\Service\ControllerUtils;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @extends AbstractCrudController<Setting>
 */
#[IsGranted('ROLE_ADMIN', statusCode: 423)]
class SettingCrudController extends AbstractCrudController
{
    private bool $settingsAddable;

    public function __construct(
        Bus $bus,
        EntityManagerInterface $entityManager,
        ControllerUtils $controllerUtils,
        bool $settingsAddable,
    ) {
        parent::__construct($bus, $entityManager, $controllerUtils);

        $this->settingsAddable = $settingsAddable;
    }

    public static function getEntityFqcn(): string
    {
        return Setting::class;
    }

    public function getEntityClass(): string
    {
        return self::getEntityFqcn();
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        if (false === $this->settingsAddable) {
            $actions->disable(Action::DELETE, Action::NEW);
        }

        return $actions;
    }

    public function getEntityFields(): array
    {
        return ['name', 'value', 'slug'];
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextField::new('value'),
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
