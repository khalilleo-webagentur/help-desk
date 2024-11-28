<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\TicketStatus;
use App\Repository\TicketStatusRepository;
use DateTime;

final readonly class TicketStatusService
{
    public function __construct(
        private TicketStatusRepository $ticketStatusRepository,
    ) {
    }

    public function getById(int $id): ?TicketStatus
    {
        return $this->ticketStatusRepository->find($id);
    }

    public function getOneByName(?string $name): ?TicketStatus
    {
        if (empty($name)) {return null;}

        return $this->ticketStatusRepository->findOneBy(['name' => $name]);
    }

    /**
     * @return TicketStatus[]
     */
    public function getAll(): array
    {
        return $this->ticketStatusRepository->findAll();
    }

    public function save(TicketStatus $model): TicketStatus
    {
        $this->ticketStatusRepository->save($model->setUpdatedAt(new DateTime()), true);

        return $model;
    }

    public function delete(TicketStatus $model): void
    {
        $this->ticketStatusRepository->remove($model, true);
    }
}
