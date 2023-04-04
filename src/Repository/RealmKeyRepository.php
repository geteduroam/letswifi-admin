<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Repository;

use App\Entity\RealmKey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RealmKey>
 * @method RealmKey|null find($id, $lockMode = null, $lockVersion = null)
 * @method RealmKey|null findOneBy(array $criteria, array $orderBy = null)
 * @method RealmKey[]    findAll()
 * @method RealmKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RealmKeyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RealmKey::class);
    }

    public function save(RealmKey $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    public function remove(RealmKey $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

//    /**
//     * @return RealmKey[] Returns an array of RealmKey objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RealmKey
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
