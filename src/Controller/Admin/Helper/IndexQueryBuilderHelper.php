<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Controller\Admin\Helper;

use App\Entity\Realm;
use App\Entity\RealmContact;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class IndexQueryBuilderHelper
{
    public function __construct() {
    }

    public function buildRealmQuery(
        QueryBuilder $queryBuilder,
        array $roles,
        int $userId
    ): QueryBuilder {
        if (in_array('ROLE_SUPER_ADMIN', $roles)) {
            return $queryBuilder;
        }

        $queryBuilder
            ->join(Realm::class, 'r', 'WITH', 'entity.realm = r.realm')
            ->join(RealmContact::class, 'rc', 'WITH', 'r.realm = rc.realm')
            ->andWhere('rc.contact = :id')
            ->setParameter('id', $userId);

        return $queryBuilder;
    }
}
