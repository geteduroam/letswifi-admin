<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Repository;

use App\Entity\Realm;
use App\Entity\RealmContact;
use App\Entity\RealmSigningLog;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RealmSigningLog>
 * @method RealmSigningLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method RealmSigningLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method RealmSigningLog[]    findAll()
 * @method RealmSigningLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RealmSigningLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RealmSigningLog::class);
    }

    public function save(RealmSigningLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    public function remove(RealmSigningLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    public function revoke(RealmSigningLog $entity, bool $flush = false): void
    {
        // Do not revoke again!
        if ($entity->getRevoked()) {
            return;
        }

        // revoke by setting the expiration date time to 'now'
        $entity->setRevoked(new DateTime());
        $this->getEntityManager()->persist($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    public function revokeById(int $id, bool $flush = false): void
    {
        $entity = $this->find($id);
        $this->revoke($entity, $flush);
    }

    /** @return array<RealmSigningLog>|null */
    public function findByUserId(int $id): array|null
    {
        return $this->createQueryBuilder('rs')
            ->join(Realm::class, 'r', 'WITH', 'rs.realm = r.realm')
            ->join(RealmContact::class, 'rc', 'WITH', 'r.realm = rc.realm')
            ->andWhere('rc.contact = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();
    }

    /** @return array<RealmSigningLog>|null */
    public function findByRequesterAndRealm(string $requester, string $realm): array|null
    {
        return $this->createQueryBuilder('rs')
            ->andWhere('rs.requester = :requester')
            ->andWhere('rs.realm = :realm')
            ->setParameter('requester', $requester)
            ->setParameter('realm', $realm)
            ->getQuery()
            ->getResult();
    }
}
