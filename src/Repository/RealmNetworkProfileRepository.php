<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Repository;

use App\Entity\NetworkProfile;
use App\Entity\Realm;
use App\Entity\RealmNetworkProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RealmNetworkProfile>
 * @method RealmNetworkProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method RealmNetworkProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method RealmNetworkProfile[]    findAll()
 * @method RealmNetworkProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RealmNetworkProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RealmNetworkProfile::class);
    }

    public function save(RealmNetworkProfile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    public function merge(RealmNetworkProfile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->merge($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    public function remove(RealmNetworkProfile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    /** @param array<NetworkProfile> $networkProfiles */
    public function saveByNetworkProfiles(
        string $realm,
        array $networkProfiles,
        bool $flush = false,
    ): void {
        $realmEntity = $this->getEntityManager()->getRepository(Realm::class)->find($realm);

        /** remove first and then insert new RealmNetworkProfile */
        $entities = $this->findBy(['realm' => $realm]);

        foreach ($entities as $entity) {
            $realmEntity->removeRealmNetworkProfile($entity);
            $this->remove($entity, $flush);
        }

        foreach ($networkProfiles as $networkProfile) {
            $entity = new RealmNetworkProfile();

            $realmEntity = new Realm();
            $realmEntity->setRealm($realm);

            $networkProfileEntity = $this->getEntityManager()
                ->getRepository(NetworkProfile::class)->find($networkProfile);

            $entity->setRealm($realmEntity);
            $entity->setNetworkProfile($networkProfileEntity);

            $this->merge($entity, $flush);

            $realmEntity->addRealmNetworkProfile($entity);
        }
    }
}
