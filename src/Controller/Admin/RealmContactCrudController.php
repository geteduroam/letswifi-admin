<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Controller\Admin;

use App\Controller\Admin\Helper\ContactHelper;
use App\Controller\Admin\Helper\RealmHelper;
use App\Entity\RealmContact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Exception;

class RealmContactCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly RealmHelper $realmHelper,
        private readonly ContactHelper $contactHelper,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return RealmContact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_ADMIN')
            ->setPageTitle('index', 'AdminRealms');
    }

    /**
     * @return FieldInterface[]
     * @psalm-return iterable<FieldInterface>
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('realm')
                ->formatValue(static function ($value, $entity) {
                    return $entity->getRealm()->getRealm();
                }),
            AssociationField::new('contact')
                ->formatValue(static function ($value, $entity) {
                    return $entity->getContact()->getUserId();
                }),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::SAVE_AND_CONTINUE)
            ->disable(Action::EDIT)
            ->add(Crud::PAGE_NEW, Action::INDEX);
    }

    /** @throws Exception */
    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
            ->add(ChoiceFilter::new('realm')
                ->setChoices($this->realmHelper->getAllRealms()))
            ->add(ChoiceFilter::new('contact')
                ->setChoices($this->contactHelper->getAllContacts()));
    }

    /** @return array<string> */
    public function getContactsChoicesOfUser(): array
    {
        return $this->realmHelper->getAllRealms();
    }
}
