<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\TicketType;
use App\Repository\TicketTypeRepository;
use DateTime;

final readonly class TicketTypesService
{
    public function __construct(
        private TicketTypeRepository $ticketTypeRepository,
    ) {
    }

    public function getById(int $id): ?TicketType
    {
        return $this->ticketTypeRepository->find($id);
    }

    public function getOneByName(string $name): ?TicketType
    {
        return $this->ticketTypeRepository->findOneBy(['name' => $name]);
    }

    /**
     * @return TicketType[]
     */
    public function getAll(): array
    {
        return $this->ticketTypeRepository->findAll();
    }

    public function save(TicketType $model): TicketType
    {
        $this->ticketTypeRepository->save($model->setUpdatedAt(new DateTime()), true);

        return $model;
    }

    public function delete(TicketType $model): void
    {
        $this->ticketTypeRepository->remove($model, true);
    }
}
