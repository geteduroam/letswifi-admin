<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Controller\Admin;

use App\Controller\Admin\Helper\IndexQueryBuilderHelper;
use App\Controller\Admin\Helper\RealmHelper;
use App\Controller\Admin\Helper\RealmSigningUserHelper;
use App\Entity\Contact;
use App\Entity\RealmSigningUser;
use App\Security\SamlBundle\Identity;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class RealmSigningUserCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly RealmHelper $realmHelper,
        private readonly RealmSigningUserHelper $realmSigningUserHelper,
        protected readonly IndexQueryBuilderHelper $indexQueryBuilderHelper,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return RealmSigningUser::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPaginatorUseOutputWalkers(true)
            ->setPaginatorFetchJoinCollection(true)
            ->setEntityPermission('ROLE_ADMIN')
            ->setDefaultSort(['lastValid' => 'DESC'])
            ->setPageTitle('index', 'User views');
    }

    /** @throws Exception */
    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
            ->add(ChoiceFilter::new('realm')
                ->setChoices($this->getRealmsChoicesOfUser()))
            ->add(TextFilter::new('requester', 'Requester'))
            ->add(NumericFilter::new('accounts', 'Accounts'))
            ->add(DateTimeFilter::new('firstIssued', 'FirstIssued'))
            ->add(DateTimeFilter::new('lastValid', 'LastValid'));
    }

    /**
     * @return FieldInterface[]
     * @psalm-return iterable<FieldInterface>
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('requester'),
            AssociationField::new('realm')
                ->formatValue(static function ($value, $entity) {
                    return $entity->getRealm()->getRealm();
                }),
            NumberField::new('accounts', 'Accounts'),
            NumberField::new('openAccounts', 'ValidAccounts'),
            DateTimeField::new('firstIssued', 'FirstIssued')
                ->setFormat('yyyy-MM-dd'),
            DateTimeField::new('lastValid', 'LastValid')
                ->setFormat('yyyy-MM-dd'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $revoke = Action::new('revoke', 'Revoke')
            ->linkToCrudAction('revokeRealmSigningUser')
            ->addCssClass('confirm-action')
            ->setHtmlAttributes([
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-confirm',
            ])
            ->displayIf(static function ($entity) {
                return $entity->getAccounts() !== $entity->getClosedAccounts();
            });

        return parent::configureActions($actions)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->disable(Action::EDIT)
            ->disable(Action::NEW)
            ->disable(Action::DELETE)
            ->disable(Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $revoke)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureAssets(Assets $assets): Assets
    {
        $assets->addWebpackEncoreEntry('app');

        return parent::configureAssets($assets);
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters,
    ): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if (
            $this->getUser() !== null &&
            ($this->getUser() instanceof Contact || $this->getUser() instanceof Identity)
        ) {
            return $this->indexQueryBuilderHelper->buildRealmQuery(
                $queryBuilder,
                $this->getUser()->getRoles(),
                $this->getUser()->getId(),
            );
        }

        return $queryBuilder;
    }

    /**
     * LinkToCrudAction to revoke one realm signing log, configured at configureAction
     */
    public function revokeRealmSigningUser(AdminContext $context): Response
    {
        $entity = $context->getEntity()->getInstance();
        $this->denyAccessUnlessGranted('edit', $entity);

        $this->realmSigningUserHelper->revoke($entity);

        if ($context->getRequest()->headers->get('referer') !== null) {
            return $this->redirect($context->getRequest()->headers->get('referer'));
        }

        return $this->redirectToRoute('overview');
    }

    /** @return array<string> */
    public function getRealmsChoicesOfUser(): array
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->realmHelper->getAllRealms();
        }

        if ($this->getUser() !== null) {
            return $this->realmHelper->getUserRealms($this->getUser());
        }

        return [];
    }
}
