<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RealmHelpdesk;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RealmHelpdesk>
 * @method RealmHelpdesk|null find($id, $lockMode = null, $lockVersion = null)
 * @method RealmHelpdesk|null findOneBy(array $criteria, array $orderBy = null)
 * @method RealmHelpdesk[]    findAll()
 * @method RealmHelpdesk[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RealmHelpdeskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RealmHelpdesk::class);
    }

    public function save(RealmHelpdesk $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    public function remove(RealmHelpdesk $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }
}
