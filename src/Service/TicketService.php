<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use DateTime;

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

    public function save(Ticket $model): Ticket
    {
        $this->ticketRepository->save($model->setUpdatedAt(new DateTime()), true);

        return $model;
    }
}
