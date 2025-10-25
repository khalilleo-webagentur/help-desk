<?php

namespace App\Repository;

use App\Entity\DeletableFile;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DeletableFile>
 */
class DeletableFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeletableFile::class);
    }

    public function save(DeletableFile $entity, bool $flush): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DeletableFile $entity, bool $flush): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return DeletableFile[]
     */
    public function findAllByCriteria(string $directory, DateTime $from, DateTime $to): array
    {
        return $this->createQueryBuilder('t1')
            ->where('t1.directory = :directory')
            ->setParameter('directory', $directory)
            ->andWhere('t1.createdAt >= :from')
            ->setParameter('from', $from)
            ->andWhere('t1.createdAt <= :to')
            ->setParameter('to', $to)
            ->orderBy('t1.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
