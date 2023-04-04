<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Controller\Admin;

use App\Entity\Contact;
use App\Entity\Realm;
use App\Entity\RealmSigningLog;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function count;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
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
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('bundles/easyAdminBundle/dashboard.html.twig', [
            'realms' => $this->getRealms(),
            'users' => $this->countUsers(),
            'pseudoAccounts' => $this->getPseudoAccounts(),
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

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            yield MenuItem::linkToCrud('Configuration', 'fas fa-gear', Realm::class);
        }

        yield MenuItem::linkToCrud('Pseudo accounts', 'fas fa-users', RealmSigningLog::class);

        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            return;
        }

        yield MenuItem::linkToCrud('Admins', 'fas fa-user', Contact::class);
    }

    private function getRealms(): int
    {
        return $this->doctrine->getRepository(
            Realm::class,
        )->countRealmsForRole($this->getUser()->getRoles(), $this->getUser()->getId());
    }

    private function countUsers(): int
    {
        return $this->doctrine->getRepository(
            RealmSigningLog::class,
        )->countRealmSigningLogsForRole($this->getUser()->getRoles(), $this->getUser()->getId());
    }

    private function getPseudoAccounts(): int
    {
        return count($this->doctrine->getRepository(
            RealmSigningLog::class,
        )->findByUserIdGroupByRequester($this->getUser()->getId()));
    }
}
