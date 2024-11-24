<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Ticket;
use App\Entity\User;
use App\Repository\TicketRepository;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class TicketService
{
    public function __construct(
        private TicketRepository $ticketRepository,
    ) {
    }

    public function getById(int $id): ?Ticket
    {
        return $this->ticketRepository->find($id);
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
    public function getAllByUser(User|UserInterface $user): array
    {
        return $this->ticketRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);
    }

    public function getAll(): array
    {
        return $this->ticketRepository->findBy([], ['createdAt' => 'DESC']);
    }

    public function save(Ticket $model): Ticket
    {
        $this->ticketRepository->save($model->setUpdatedAt(new DateTime()), true);

        return $model;
    }
}
