<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Controller\Admin;

use _PHPStan_1f608dc6a\Nette\Neon\Exception;
use App\Entity\Contact;
use App\Entity\Realm;
use App\Entity\RealmSigningLog;
use App\Entity\RealmSigningUser;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function count;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
        $connection = $this->doctrine->getConnection();
        $connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }

    /** @throws Exception */
    #[Route('/', name: 'overview')]
    public function entrance(): Response
    {
        return $this->redirectToRoute('admin_default_locale');
    }

    /** @throws Exception */
    #[Route('/admin', name: 'admin_default_locale')]
    public function indexDefaultLocale(): Response
    {
        if (!($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_ADMIN'))) {
            throw new Exception('You have no access to this resource');
        }

        //return $this->render('@EasyAdmin/page/content.html.twig');
        return $this->render('bundles/easyAdminBundle/dashboard.html.twig', [
            'realms' => $this->getRealms(),
            'pseudoAccounts' => $this->getPseudoAccounts(),
            'userAccounts' => $this->getUserAccounts(),
        ]);
    }

    /** @throws Exception */
    #[Route('/admin/{_locale}', name: 'admin')]
    public function index(): Response
    {
        if (!($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_ADMIN'))) {
            throw new Exception('You have no access to this resource');
        }

        //return $this->render('@EasyAdmin/page/content.html.twig');
        return $this->render('bundles/easyAdminBundle/dashboard.html.twig', [
            'realms' => $this->getRealms(),
            'pseudoAccounts' => $this->getPseudoAccounts(),
            'userAccounts' => $this->getUserAccounts(),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        $name    = $this->getParameter('app.name');
        $favicon = $this->getParameter('app.favicon');

        return Dashboard::new()
            ->setTitle($name)
            ->setFaviconPath($favicon)
            ->disableDarkMode()
            ->setLocales(['en', 'nl']);
    }

    /**
     * @return MenuItemInterface[]
     * @psalm-return iterable<MenuItemInterface>
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-dashboard');
        yield MenuItem::linkToCrud('Configuration', 'fas fa-gear', Realm::class)
            ->setPermission('ROLE_SUPER_ADMIN');

        yield MenuItem::linkToCrud('PseudoAccounts', 'fas fa-users', RealmSigningLog::class);
        yield MenuItem::linkToCrud('UserAccounts', 'fas fa-user-circle', RealmSigningUser::class);
        yield MenuItem::linkToCrud('Admins', 'fas fa-user-cog', Contact::class)
            ->setPermission('ROLE_SUPER_ADMIN');
    }

    private function getRealms(): int
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->doctrine->getRepository(Realm::class)->count([]);
        }

        return count($this->doctrine->getRepository(
            Realm::class,
        )->findByUser($this->getUser()->getId()));
    }

    private function getPseudoAccounts(): int
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->doctrine->getRepository(RealmSigningLog::class)->count([]);
        }

        return count($this->doctrine->getRepository(
            RealmSigningLog::class,
        )->findByUserId($this->getUser()->getId()));
    }

    private function getUserAccounts(): int
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->doctrine->getRepository(RealmSigningUser::class)->count([]);
        }

        return count($this->doctrine->getRepository(
            RealmSigningUser::class,
        )->findByUserId($this->getUser()->getId()));
    }
}
