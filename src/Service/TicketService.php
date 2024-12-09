<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Entity\Ticket;
use App\Entity\TicketStatus;
use App\Entity\User;
use App\Repository\TicketRepository;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class TicketService
{
    public function __construct(
        private TicketRepository $ticketRepository,
        private UserService $userService,
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

    public function getOneByCustomerAndTicketNo(UserInterface $user, int $no): ?Ticket
    {
        return $this->ticketRepository->findOneBy(['customer' => $user, 'ticketNo' => $no]);
    }

    public function getOneByCustomerAndId(UserInterface $user, int $id): ?Ticket
    {
        return $this->ticketRepository->findOneBy(['customer' => $user, 'id' => $id]);
    }

    public function getLatTicketNo(): int
    {
        $ticket = $this->ticketRepository->findOneBy([], ['createdAt' => 'DESC']);

        $now = new DateTime();

        return $ticket && $ticket->getTicketNo() > 0 ? $ticket->getTicketNo() +1 : (int)($now->format('Y') . '001');
    }

    /**
     * @return Ticket[]
     */
    public function getAllByCompanyAndStatus(Company $company, ?TicketStatus $status): array
    {
        $issues = [];

        foreach ($this->userService->getAllByCompany($company) as $user) {
            $issue = $this->ticketRepository->findBy(['customer' => $user, 'status' => $status], ['createdAt' => 'DESC']);
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
        return $this->ticketRepository->findBy(['status' => $status], ['createdAt' => 'DESC']);
    }

    public function countAllByUser(UserInterface $user): int
    {
        return $this->ticketRepository->count(['customer' => $user]);
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
