<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Entity\Project;
use App\Entity\Ticket;
use App\Entity\TicketStatus;
use App\Repository\TicketRepository;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class TicketService
{
    public function __construct(
        private TicketRepository $ticketRepository,
        private UserService      $userService,
    ) {
    }

    public function getById(int $id): ?Ticket
    {
        return $this->ticketRepository->find($id);
    }

    public function getByTicketNo(int $no): ?Ticket
    {
        return $this->ticketRepository->findOneBy(['ticketNo' => $no]);
    }

    /**
     * @deprecated
     * @use TicketService::getOneByProjectAndId()
     */
    public function getOneByCustomerAndId(UserInterface $user, int $id): ?Ticket
    {
        return $this->ticketRepository->findOneBy(['customer' => $user, 'id' => $id]);
    }

    public function getOneByProjectAndId(Project $project, int $id): ?Ticket
    {
        return $this->ticketRepository->findOneBy(['project' => $project, 'id' => $id]);
    }

    public function getLatTicketNo(): int
    {
        $ticket = $this->ticketRepository->findOneBy([], ['createdAt' => 'DESC']);

        $now = new DateTime();

        return $ticket && $ticket->getTicketNo() > 0 ? $ticket->getTicketNo() + 1 : (int)($now->format('Y') . '001');
    }

    /**
     * @return Ticket[]
     */
    public function getAllByCompanyAndStatus(Company $company, ?TicketStatus $status): array
    {
        $issues = [];

        foreach ($this->userService->getAllByCompany($company) as $user) {
            $issue = $status !== null
                ? $this->ticketRepository->findBy(['customer' => $user, 'status' => $status], ['createdAt' => 'DESC'])
                : $this->ticketRepository->findBy(['customer' => $user], ['createdAt' => 'DESC']);
            if (null !== $issue) {
                if (is_array($issue)) {
                    foreach ($issue as $issueItem) {
                        $issues[] = $issueItem;
                    }
                } else {
                    $issues[] = $issue;
                }
            }
        }

        return $issues;
    }

    public function getAllByStatus(?TicketStatus $status): array
    {
        return null !== $status
            ? $this->ticketRepository->findBy(['status' => $status], ['createdAt' => 'DESC'])
            : $this->ticketRepository->findBy([], ['createdAt' => 'DESC']);
    }

    public function countAllByUser(UserInterface $user): int
    {
        return $this->ticketRepository->count(['customer' => $user]);
    }

    /**
     * @return Ticket[]
     */
    public function getAllByCompany(Company $company): array
    {
        return $this->ticketRepository->findAllByCompany($company);
    }

    public function countAllByCompany(Company $company): int
    {
        return count($this->getAllByCompany($company));
    }

    public function countAllByCompanyAndStatus(Company $company, TicketStatus $status): int
    {
        return count($this->ticketRepository->findAllByCompanyAndStatus($company, $status));
    }

    public function countAll(): int
    {
        return $this->ticketRepository->count();
    }

    public function countStatusByUser(UserInterface $user, ?TicketStatus $status): int
    {
        return $this->ticketRepository->count(['customer' => $user, 'status' => $status]);
    }

    public function countStatus(?TicketStatus $status): int
    {
        return $this->ticketRepository->count(['status' => $status]);
    }

    public function save(Ticket $model): Ticket
    {
        $this->ticketRepository->save($model->setUpdatedAt(new DateTime()), true);

        return $model;
    }
}
