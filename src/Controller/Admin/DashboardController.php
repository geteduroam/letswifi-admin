<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Controller\Admin;

use App\Entity\Contact;
use App\Entity\NetworkProfile;
use App\Entity\Realm;
use App\Entity\RealmContact;
use App\Entity\RealmHelpdesk;
use App\Entity\RealmSigningLog;
use App\Entity\RealmSigningUser;
use App\Entity\VhostRealm;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

use function count;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly Security $security,
        private readonly RequestStack $requestStack,
    ) {
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
        return $this->render('bundles/EasyAdminBundle/dashboard.html.twig', [
            'realmCount' => $this->countRealms(),
            'userCount' => $this->countUsers(),
            'pseudoAccountCount' => $this->countPseudoAccounts(),
        ]);
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
        yield MenuItem::subMenu('Configuration', 'fa fas fa-gear')
            ->setSubItems([
                MenuItem::linkToCrud('Configuration', 'fas fa-gear', Realm::class)
                    ->setPermission('ROLE_SUPER_ADMIN'),
                MenuItem::linkToCrud('NetworkProfile', 'fas fa-network-wired', NetworkProfile::class)
                    ->setPermission('ROLE_SUPER_ADMIN'),
                MenuItem::linkToCrud('VhostRealm', 'fas fa-server', VhostRealm::class)
                    ->setPermission('ROLE_SUPER_ADMIN'),
                MenuItem::linkToCrud('Helpdesk', 'fas fa-hands-helping', RealmHelpdesk::class)
                    ->setPermission('ROLE_SUPER_ADMIN'),
            ])->setPermission('ROLE_SUPER_ADMIN');

        yield MenuItem::linkToCrud('PseudoAccounts', 'fas fa-users', RealmSigningLog::class);
        yield MenuItem::linkToCrud('UserAccounts', 'fas fa-user-circle', RealmSigningUser::class);

        yield MenuItem::subMenu('Administrator', 'fa fas fa-user')
            ->setSubItems([
                MenuItem::linkToCrud('Accounts', 'fas fa-user-cog', Contact::class)
                    ->setPermission('ROLE_SUPER_ADMIN'),
                MenuItem::linkToCrud('Realms', 'fas fa-toolbox', RealmContact::class)
            ->setPermission('ROLE_SUPER_ADMIN'),
            ])->setPermission('ROLE_SUPER_ADMIN');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $firewallName = '';
        $request      = $this->requestStack->getCurrentRequest();
        if ($request !== null) {
            $firewallName = $this->security->getFirewallConfig($request)?->getName();
        }

        if ($firewallName === 'main') {
            return parent::configureUserMenu($user);
        }

        /** No logout otherwise */
        return UserMenu::new()
            ->displayUserName()
            ->displayUserAvatar()
            ->setName($user->getUserIdentifier())
            ->setAvatarUrl(null);
    }

    /** @throws Exception */
    private function countRealms(): int
    {
        if (! $this->getUser() instanceof UserInterface) {
            return 0;
        }

        return $this->doctrine->getRepository(
            Realm::class,
        )->countRealmsForRole($this->getUser()->getRoles(), $this->getUser()->getId());
    }

    private function countPseudoAccounts(): int
    {
        if (! $this->getUser() instanceof UserInterface) {
            return 0;
        }

        return $this->doctrine->getRepository(
            RealmSigningLog::class,
        )->countRealmSigningLogsForRole($this->getUser()->getRoles(), $this->getUser()->getId());
    }

    private function countUsers(): int
    {
        if (! $this->getUser() instanceof UserInterface) {
            return 0;
        }

        return $this->doctrine->getRepository(RealmSigningLog::class)
            ->findByUserIdGroupByRequester($this->getUser()->getId());
    }
}
