<?php

namespace App\Repository;

use App\Entity\SystemLog;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
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
     * Job
     * @return SystemLog[]
     */
    public function findAllByCriteria(string $event, DateTimeInterface $from, DateTimeInterface $to): array
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();
        $qb->select('t1')
            ->from(SystemLog::class, 't1')
            ->where('t1.createdAt BETWEEN :from AND :to')
            ->setParameters(new ArrayCollection([
                new Parameter('from', $from, 'datetime'),
                new Parameter('to', $to, 'datetime')
            ]));

        if ($event) {
            $qb
                ->andWhere($qb->expr()->like('t1.event', ':event'))
                ->setParameter('event', '%' . $event . '%');
        }

        return $qb
            ->getQuery()
            ->getResult();
    }
}
