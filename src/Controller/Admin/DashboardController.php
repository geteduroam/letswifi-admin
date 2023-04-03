<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Controller\Admin;

use App\Entity\RealmSigningLog;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
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

        return $this->render('@EasyAdmin/page/content.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        $name    = $this->getParameter('app.name');
        $favicon = $this->getParameter('app.favicon');

        return Dashboard::new()
            ->setTitle($name)
            ->setFaviconPath($favicon)
            ->setLocales(['en', 'nl']);
    }

    /**
     * @return MenuItemInterface[]
     * @psalm-return iterable<MenuItemInterface>
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-dashboard');
        yield MenuItem::linkToCrud('Users', 'fas fa-list', RealmSigningLog::class);
    }
}
