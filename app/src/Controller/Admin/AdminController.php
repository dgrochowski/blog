<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Bus\Bus;
use App\Entity\Category;
use App\Entity\File;
use App\Entity\Post;
use App\Entity\Setting;
use App\Entity\Social;
use App\Entity\Tag;
use App\Entity\User;
use App\Service\LogReader;
use App\Service\SystemInfoService;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class AdminController extends AbstractDashboardController
{
    public function __construct(
        protected Bus $bus,
        protected AuthenticationUtils $authenticationUtils,
        protected Security $security,
        protected SystemInfoService $systemInfoService,
        protected LogReader $logReader,
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

    #[IsGranted('ROLE_ADMIN', statusCode: 423)]
    #[Route('/info', name: 'info')]
    public function info(): Response
    {
        return $this->render('admin/info.html.twig', $this->systemInfoService->getSystemInfo());
    }

    #[IsGranted('ROLE_ADMIN', statusCode: 423)]
    #[Route('/debug', name: 'debug')]
    public function debug(): Response
    {
        try {
            $logs = $this->logReader->getLogs(1, 200);
        } catch (\Throwable $e) {
            $logs = [];
        }

        return $this->render('admin/debug.html.twig', [
            'logs' => $logs,
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
        yield MenuItem::linkToCrud('Socials', 'fa fa-thumbs-up', Social::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Files', 'fa fa-image', File::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Settings', 'fa fa-gear', Setting::class)
            ->setPermission('ROLE_ADMIN');

        yield MenuItem::section('System')
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToRoute('Info', 'fa fa-info', 'info')
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToRoute('Debug log', 'fa fa-book', 'debug')
            ->setPermission('ROLE_ADMIN');

        yield MenuItem::section();
        yield MenuItem::linkToUrl('Github', 'fa-brands fa-github', 'https://github.com/dgrochowski/blog');
    }
}
