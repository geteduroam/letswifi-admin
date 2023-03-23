<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\VhostRealm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VhostRealm>
 * @method VhostRealm|null find($id, $lockMode = null, $lockVersion = null)
 * @method VhostRealm|null findOneBy(array $criteria, array $orderBy = null)
 * @method VhostRealm[]    findAll()
 * @method VhostRealm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VhostRealmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VhostRealm::class);
    }

    public function save(VhostRealm $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    public function remove(VhostRealm $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }
}
