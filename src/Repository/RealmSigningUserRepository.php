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
use App\Entity\RealmSigningUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RealmSigningUser>
 * @method RealmSigningUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method RealmSigningUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method RealmSigningUser[]    findAll()
 * @method RealmSigningUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RealmSigningUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RealmSigningUser::class);
    }

    public function save(RealmSigningUser $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    public function remove(RealmSigningUser $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    /** @return array<RealmSigningUser> */
    public function findByUserId(int $id): array
    {
        return $this->createQueryBuilder('rs')
            ->join(Realm::class, 'r', 'WITH', 'rs.realm = r.realm')
            ->join(RealmContact::class, 'rc', 'WITH', 'r.realm = rc.realm')
            ->andWhere('rc.contact = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();
    }
}
