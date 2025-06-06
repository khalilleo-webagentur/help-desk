<?php

namespace App\Repository;

use App\Entity\SystemLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SystemLog>
 */
class SystemLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemLog::class);
    }

    public function save(SystemLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SystemLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return SystemLog[]
     */
    public function findTDeleteAllByCriteria(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        return $this->createQueryBuilder('t1')
            ->where('t1.createdAt >= :from')
            ->setParameter('from', $from)
            ->andWhere('t1.createdAt <= :to')
            ->setParameter('to', $to)
            ->orderBy('t1.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
