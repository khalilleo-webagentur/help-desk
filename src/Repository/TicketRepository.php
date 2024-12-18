<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Ticket;
use App\Entity\TicketStatus;
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
}
