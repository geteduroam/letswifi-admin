<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Controller\Admin;

use App\Controller\Admin\Helper\IndexQueryBuilderHelper;
use App\Entity\Realm;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class RealmCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly IndexQueryBuilderHelper $indexQueryBuilderHelper,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Realm::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_ADMIN')
            ->setPageTitle('index', 'Configuration');
    }

    /**
     * @return FieldInterface[]
     * @psalm-return iterable<FieldInterface>
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('realm')
                ->setFormTypeOption('disabled', 'disabled'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $edit = Action::new('update', 'Edit')
            ->linkToCrudAction('renderRealm');

        return parent::configureActions($actions)
            ->disable(Action::EDIT)
            ->disable(Action::NEW)
            ->disable(Action::DELETE)
            ->add(Crud::PAGE_INDEX, $edit);
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
     * LinkToCrudAction to editRealm, configured at configureAction
     */
    public function renderRealm(AdminContext $context): Response
    {
        $entity = $context->getEntity()->getInstance();
        $this->denyAccessUnlessGranted('edit', $entity);

        $url = $this->adminUrlGenerator->setRoute(
            'app_realm',
            [
                'entityId' => $entity->getRealm(),
                'referrer' => $context->getReferrer(),
            ],
        )->generateUrl();

        return $this->redirect($url);
    }
}
