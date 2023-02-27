<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Controller\Admin;

use App\Command\RealmCommand;
use App\Command\RealmSigningLogCommand;
use App\Entity\Realm;
use App\Entity\RealmContact;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RealmSigningLogCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RealmCommand $realmCommand,
        private readonly RealmSigningLogCommand $realmSigningLogCommand,
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
            ->setPageTitle('index', 'Users');
    }

    /**
     * @return FieldInterface[]
     * @psalm-return iterable<FieldInterface>
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('serial'),
            TextField::new('requester'),
            TextField::new('subjectWithoutCustomerName')
                ->setLabel('Pseudo account')
                ->hideOnIndex(),
            AssociationField::new('realm')
                ->formatValue(static function ($value, $entity) {
                    return $entity->getRealm()->getRealm();
                }),
            TextField::new('client')
                ->formatValue(static function ($value, $entity) {
                    return $value ? $value : '-';
                }),
            DateTimeField::new('issued')
                ->setFormat('yyyy-MM-dd HH:mm:ss')
                ->formatValue(static function ($value, $entity) {
                    return $value ? $value : '-';
                }),
            DateTimeField::new('revoked')
                ->setFormat('yyyy-MM-dd HH:mm:ss')
                ->formatValue(static function ($value, $entity) {
                    return $value ? $value : '-';
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
                ]);

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
        $assets->addJsFile('assets/js/confirm-modal.js');

        return parent::configureAssets($assets);
    }

    /** @throws Exception */
    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
            ->add(ChoiceFilter::new('realm')
                ->setChoices($this->getRealmsChoicesOfUser()))
            ->add(DateTimeFilter::new('revoked'));
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters,
    ): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return $queryBuilder;
        }

        $user = $this->tokenStorage->getToken()->getUser();

        $queryBuilder
            ->join(Realm::class, 'r', 'WITH', 'entity.realm = r.realm')
            ->join(RealmContact::class, 'rc', 'WITH', 'r.realm = rc.realm')
            ->andWhere('rc.contact = :id')
            ->setParameter('id', $user->getId());

        return $queryBuilder;
    }

    /**
     * LinkToCrudAction to revoke one realm signing log, configured at configureAction
     */
    public function revokeRealmSigningLog(AdminContext $context): Response
    {
        $entity = $context->getEntity()->getInstance();
        $this->denyAccessUnlessGranted('edit', $entity);

        $this->realmSigningLogCommand->revoke($entity);

        return $this->redirect($context->getReferrer());
    }

    /**
     * LinkToCrudAction to revoke multiple (batch) realm signing logs, configured at configureActions
     */
    public function revokeRealmSigningLogBatch(BatchActionDto $batchActionDto): Response
    {
        $this->realmSigningLogCommand->revokeBatch($batchActionDto->getEntityIds());

        return $this->redirect($batchActionDto->getReferrerUrl());
    }

    /** @return array<Realm> */
    public function getRealmsChoicesOfUser(): array
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->realmCommand->getAllRealms();
        }

        return $this->realmCommand->getUserRealms($this->tokenStorage->getToken()->getUser());
    }
}
