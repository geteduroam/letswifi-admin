<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Repository;

use App\Entity\CA;
use App\Entity\Realm;
use App\Entity\RealmTrust;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RealmTrust>
 * @method RealmTrust|null find($id, $lockMode = null, $lockVersion = null)
 * @method RealmTrust|null findOneBy(array $criteria, array $orderBy = null)
 * @method RealmTrust[]    findAll()
 * @method RealmTrust[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RealmTrustRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RealmTrust::class);
    }

    public function save(RealmTrust $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    public function merge(RealmTrust $entity, bool $flush = false): void
    {
        $this->getEntityManager()->merge($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    public function remove(RealmTrust $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    /** @param array<string> $cas */
    public function saveTrustsByRealm(
        string $realm,
        array $cas,
        bool $flush = false,
    ): void {
        $realmEntity = $this->getEntityManager()->getRepository(Realm::class)->find($realm);

        /** remove first and then insert new trust */
        $entities = $this->findBy(['realm' => $realm]);

        foreach ($entities as $entity) {
            $realmEntity->removeRealmTrust($entity);
            $this->remove($entity, $flush);
        }

        foreach ($cas as $ca) {
            $entity = new RealmTrust();

            $realmEntity = new Realm();
            $realmEntity->setRealm($realm);

            $caEntity = $this->getEntityManager()->getRepository(CA::class)->find($ca);

            $entity->setRealm($realmEntity);
            $entity->setTrustedCaSub($caEntity);

            $this->merge($entity, $flush);

            $realmEntity->addRealmTrust($entity);
        }
    }
}
