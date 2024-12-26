<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\TicketPriority;
use App\Repository\TicketPriorityRepository;

final readonly class TicketPriorityService
{
    public function __construct(
        private TicketPriorityRepository $ticketPriorityRepository,
    ) {
    }

    public function getById(int $id): ?TicketPriority
    {
        return $this->ticketPriorityRepository->find($id);
    }

    public function getOneByName(string $name): ?TicketPriority
    {
        return $this->ticketPriorityRepository->findOneBy(['name' => $name]);
    }

    /**
     * @return TicketPriority[]
     */
    public function getAll(): array
    {
        return $this->ticketPriorityRepository->findBy([], ['id' => 'DESC']);
    }

    public function save(TicketPriority $ticketPriority): TicketPriority
    {
        $this->ticketPriorityRepository->save($ticketPriority->setUpdatedAt(new \DateTime()), true);

        return $ticketPriority;
    }

    public function delete(TicketPriority $ticketPriority): void
    {
        $this->ticketPriorityRepository->remove($ticketPriority, true);
    }
}