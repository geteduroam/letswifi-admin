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
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

use function in_array;
use function is_int;

/**
 * @extends ServiceEntityRepository<Realm>
 * @method Realm|null find($id, $lockMode = null, $lockVersion = null)
 * @method Realm|null findOneBy(array $criteria, array $orderBy = null)
 * @method Realm[]    findAll()
 * @method Realm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RealmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Realm::class);
    }

    public function save(Realm $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    public function remove(Realm $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    /** @return Realm[] */
    public function findByUser(int $id): array
    {
        return $this->createQueryBuilder('r')
            ->innerJoin(RealmContact::class, 'rc', 'WITH', 'r.realm = rc.realm')
            ->andWhere('rc.contact = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array<string> $roles
     *
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countRealmsForRole(array $roles, int $id): int
    {
        if (in_array('ROLE_SUPER_ADMIN', $roles, true)) {
            return $this->count([]);
        }

        $queryBuilder = $this->createQueryBuilder('r');

        $result = $queryBuilder
            ->select($queryBuilder->expr()->count('r'))
            ->innerJoin(RealmContact::class, 'rc', 'WITH', 'r.realm = rc.realm')
            ->andWhere('rc.contact = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult();
        if (is_int($result)) {
            return $result;
        }

        return 0;
    }
}
