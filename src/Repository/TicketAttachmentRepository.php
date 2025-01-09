<?php

namespace App\Repository;

use App\Entity\TicketAttachment;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TicketAttachment>
 */
class TicketAttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketAttachment::class);
    }

    public function save(TicketAttachment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TicketAttachment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return TicketAttachment[]
     */
    public function findAllByCriteria(
        ?string           $fileName,
        ?int              $userId,
        ?string           $fileNo,
        ?string           $type,
        DateTimeInterface $dateFrom,
        DateTimeInterface $dateTo
    ): array
    {
        $qb = $this->createQueryBuilder('t0')
            ->select('t1')
            ->from(TicketAttachment::class, 't1');

        if ($fileName != '') {
            $qb->andWhere('t1.filename LIKE :fileName')
                ->setParameter('fileName', '%' . $fileName . '%');
        }

        if ($userId > 0) {
            $qb->andWhere('t1.userId = :userId')
                ->setParameter('userId', $userId);
        }

        if ($fileNo != '') {
            $qb->andWhere('t1.fileNo = :fileNo')
                ->setParameter('fileNo', $fileNo);
        }

        if ($type != '') {
            $qb->andWhere('t1.extension = :type')
                ->setParameter('type', $type);
        }

        $qb->andWhere('t1.createdAt >= :dateFrom')
            ->setParameter('dateFrom', $dateFrom);
        $qb->andWhere('t1.createdAt <= :dateTo')
            ->setParameter('dateTo', $dateTo);

        return $qb->orderBy('t1.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
