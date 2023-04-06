<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Controller\Admin;

use App\Entity\VhostRealm;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class VhostRealmCrudController extends AbstractCrudController
{
    public function __construct()
    {
    }

    public static function getEntityFqcn(): string
    {
        return VhostRealm::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_SUPER_ADMIN')
            ->setPageTitle('index', 'VhostRealm');
    }

    /**
     * @return FieldInterface[]
     * @psalm-return iterable<FieldInterface>
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('httpHost')
                ->setLabel('HttpHost'),
            TextField::new('pathPrefix')
                ->setLabel('PathPrefix'),
            TextField::new('authService')
             ->hideOnIndex()
             ->setLabel('AuthService'),
            TextField::new('authConfig')
                ->hideOnIndex()
                ->setLabel('AuthConfiguration'),
            AssociationField::new('realm')
                ->formatValue(static function ($value, $entity) {
                    return $entity->getRealm()->getRealm();
                }),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
         return $actions
            ->disable(Action::SAVE_AND_CONTINUE)
            ->add(Crud::PAGE_EDIT, Action::INDEX);
    }
}
