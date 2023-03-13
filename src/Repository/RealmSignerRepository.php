<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Repository;

use App\Entity\RealmSigner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RealmSigner>
 * @method RealmSigner|null find($id, $lockMode = null, $lockVersion = null)
 * @method RealmSigner|null findOneBy(array $criteria, array $orderBy = null)
 * @method RealmSigner[]    findAll()
 * @method RealmSigner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RealmSignerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RealmSigner::class);
    }

    public function save(RealmSigner $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    public function remove(RealmSigner $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }
}
