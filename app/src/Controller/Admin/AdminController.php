<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Bus\Bus;
use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminController extends AbstractDashboardController
{
    public function __construct(
        protected Bus $bus,
        protected AuthenticationUtils $authenticationUtils,
        protected Security $security,
    ) {
    }

    #[Route('/')]
    public function index(): Response
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        return $this->render('admin/dashboard.html.twig', [
            'user' => $user?->getName() ?? 'User',
            'favicon_path' => '/favicon.ico',
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin panel')
            ->setTranslationDomain('admin')
        ;
    }

    public function configureMenuItems(): iterable
    {
        //        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Blog');
        yield MenuItem::linkToCrud('Posts', 'fa fa-newspaper', Post::class)
            ->setPermission('ROLE_EDITOR');
        yield MenuItem::linkToCrud('Categories', 'fa fa-list', Category::class)
            ->setPermission('ROLE_EDITOR');
        yield MenuItem::linkToCrud('Tags', 'fa fa-tags', Tag::class)
            ->setPermission('ROLE_EDITOR');

        yield MenuItem::section('Admin')
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class)
            ->setPermission('ROLE_ADMIN');
    }
}
