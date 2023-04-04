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
use App\Controller\Admin\Helper\RealmSigningLogHelper;
use App\Entity\Realm;
use App\Entity\RealmSigningLog;
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
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class RealmSigningLogCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly RealmHelper $realmHelper,
        private readonly RealmSigningLogHelper $realmSigningLogHelper,
        private readonly IndexQueryBuilderHelper $indexQueryBuilderHelper,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return RealmSigningLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_ADMIN')
            ->setPageTitle('index', 'PseudoAccounts');
    }

    /**
     * @return FieldInterface[]
     * @psalm-return iterable<FieldInterface>
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('serial', 'Serial'),
            TextField::new('requester'),
            TextField::new('subjectWithoutCustomerName', 'PseudoAccount'),
            AssociationField::new('realm')
                ->formatValue(static function ($value, $entity) {
                    return $entity->getRealm()->getRealm();
                }),
            DateTimeField::new('expires', 'ValidUntil')
                ->setFormat('yyyy-MM-dd')
                ->formatValue(static function ($value, $entity) {
                    return $value ?? '-';
                }),
            BooleanField::new('revoked')
                ->renderAsSwitch(false)
                ->formatValue(static function ($value, $entity) {
                    return (bool) $value;
                }),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $revoke = Action::new('revoke', 'Revoke')
                ->linkToCrudAction('revokeRealmSigningLog')
                ->addCssClass('confirm-action')
                ->setHtmlAttributes([
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#modal-confirm',
                ])
                ->displayIf(static function ($entity) {
                    return !$entity->getRevoked();
                });

        return parent::configureActions($actions)
            ->disable(Action::EDIT)
            ->disable(Action::NEW)
            ->disable(Action::DELETE)
            ->add(Crud::PAGE_INDEX, $revoke)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->addBatchAction(Action::new('revoke batch', 'Revoke batch')
            ->linkToCrudAction('revokeRealmSigningLogBatch')
            ->addCssClass('btn btn-primary')
            ->setIcon('fa fa-user-check'));
    }

    public function configureAssets(Assets $assets): Assets
    {
        $assets->addJsFile('/assets/js/confirm-modal.js');

        return parent::configureAssets($assets);
    }

    /** @throws Exception */
    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
            ->add(ChoiceFilter::new('realm')
                ->setChoices($this->getRealmsChoicesOfUser()))
            ->add(TextFilter::new('requester', 'Requester'))
            ->add(TextFilter::new('sub', 'Subject'))
            ->add(DateTimeFilter::new('expires', 'ValidUntil'));
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters,
    ): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        return $this->indexQueryBuilderHelper->buildRealmQuery(
            $queryBuilder,
            $this->getUser()->getRoles(),
            $this->getUser()->getId(),
        );
    }

    /**
     * LinkToCrudAction to revoke one realm signing log, configured at configureAction
     */
    public function revokeRealmSigningLog(AdminContext $context): Response
    {
        $entity = $context->getEntity()->getInstance();
        $this->denyAccessUnlessGranted('edit', $entity);

        $this->realmSigningLogHelper->revoke($entity);

        return $this->redirect($context->getReferrer());
    }

    /**
     * LinkToCrudAction to revoke multiple (batch) realm signing logs, configured at configureActions
     */
    public function revokeRealmSigningLogBatch(BatchActionDto $batchActionDto): Response
    {
        $this->realmSigningLogHelper->revokeBatch($batchActionDto->getEntityIds());

        return $this->redirect($batchActionDto->getReferrerUrl());
    }

    /** @return array<Realm> */
    private function getRealmsChoicesOfUser(): array
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->realmHelper->getAllRealms();
        }

        return $this->realmHelper->getUserRealms($this->getUser());
    }
}
