<?php

declare(strict_types=1);

namespace App\Repository;

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
        // revoke by setting the expiration date time to 'now'
        $entity->setRevoked(new DateTime());
        $this->getEntityManager()->persist($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }
}
