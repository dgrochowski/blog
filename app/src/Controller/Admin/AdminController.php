<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Bus\Bus;
use App\Entity\Admin;
use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Tag;
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
        /** @var Admin|null $admin */
        $admin = $this->security->getUser();

        return $this->render('admin/dashboard.html.twig', [
            'user' => $admin?->getName() ?? 'Admin',
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
        yield MenuItem::linkToCrud('Posts', 'fa fa-newspaper', Post::class);
        yield MenuItem::linkToCrud('Categories', 'fa fa-list', Category::class);
        yield MenuItem::linkToCrud('Tags', 'fa fa-tags', Tag::class);

        yield MenuItem::section('Admin');
        yield MenuItem::linkToCrud('Admin', 'fa fa-user', Admin::class);
    }
}
