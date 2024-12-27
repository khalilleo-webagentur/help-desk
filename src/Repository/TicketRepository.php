<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Ticket;
use App\Entity\TicketStatus;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function save(Ticket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ticket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * All ticket by a company
     * @return Ticket[]
     */
    public function findAllByCompany(Company $company): array
    {
        $projectIds = [];

        foreach ($company->getProjects() as $project) {
            $projectIds[] = $project->getId();
        }

        return $this->createQueryBuilder('t1')
            ->join('t1.project', 't2', 'WITH', 't2.id IN (:projectIds)')
            ->setParameter('projectIds', $projectIds)
            ->orderBy('t1.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * All ticket by a company
     * @return Ticket[]
     */
    public function findAllByCompanyAndStatus(Company $company, TicketStatus $status): array
    {
        $projectIds = [];

        foreach ($company->getProjects() as $project) {
            $projectIds[] = $project->getId();
        }

        return $this->createQueryBuilder('t1')
            ->join('t1.project', 't2', 'WITH', 't2.id IN (:projectIds)')
            ->setParameter('projectIds', $projectIds)
            ->join('t1.status', 't3', 'WITH', 't1.status = :status')
            ->setParameter('status', $status)
            ->orderBy('t1.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Filter all ticket by criteria
     * @return Ticket[]
     */
    public function findAllByCriteria(
        ?int              $projectId,
        ?int              $statusId,
        ?int              $priorityId,
        ?int              $labelId,
        ?int              $companyId,
        ?string           $issueCreatedBy,
        ?string           $issueAssignedTo,
        ?int              $issueNo,
        ?string           $issueTitle,
        DateTimeInterface $issueDateFrom,
        DateTimeInterface $issueDateTo
    ): array
    {
        $qb = $this->createQueryBuilder('t0')
            ->select('t1')
            ->from(Ticket::class, 't1');

        if ($companyId > 0) {
            $qb->join('t1.customer', 't2') // user
            ->join('t2.company', 't3', 'WITH', 't3.id = :companyId') // company
            ->setParameter('companyId', $companyId);
        }

        if ($projectId > 0) {
            $qb->join('t1.project', 't4');
            $qb->andWhere('t4.id = :projectId')
                ->setParameter('projectId', $projectId);
        }

        if ($labelId > 0) {
            $qb->join('t1.label', 't5');
            $qb->andWhere('t5.id = :labelId')
                ->setParameter('labelId', $labelId);
        }

        if ($statusId > 0) {
            $qb->join('t1.status', 't6');
            $qb->andWhere('t6.id = :statusId')
                ->setParameter('statusId', $statusId);
        }

        if ($issueCreatedBy != "") {
            $qb->join('t1.customer', 't7') // user
            ->andWhere('t7.name LIKE :issueCreatedBy')
                ->setParameter('issueCreatedBy', '%' . $issueCreatedBy . '%');
        }

        if ($issueAssignedTo != "") {
            $qb->join('t1.assignee', 't8')
                ->andWhere('t8.name LIKE :issueAssignedTo')
                ->setParameter('issueAssignedTo', '%' . $issueAssignedTo . '%');
        }

        if ($priorityId > 0) {
            $qb->join('t1.priority', 't9');
            $qb->andWhere('t9.id = :priorityId')
                ->setParameter('priorityId', $priorityId);
        }

        if ($issueNo > 0) {
            $qb->andWhere('t1.ticketNo = :issueNo')
                ->setParameter('issueNo', $issueNo);
        }

        if ($issueTitle != '') {
            $qb->andWhere('t1.title LIKE :title')
                ->setParameter('title', '%' . $issueTitle . '%');
        }

        $qb->andWhere('t1.createdAt >= :issueDateFrom')
            ->setParameter('issueDateFrom', $issueDateFrom);
        $qb->andWhere('t1.createdAt <= :issueDateTo')
            ->setParameter('issueDateTo', $issueDateTo);

        return $qb->orderBy('t1.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
