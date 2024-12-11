<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\TicketLabel;
use App\Repository\TicketLabelRepository;
use DateTime;

final readonly class TicketLabelsService
{
    public function __construct(
        private TicketLabelRepository $ticketLabelRepository,
    ) {
    }

    public function getById(int $id): ?TicketLabel
    {
        return $this->ticketLabelRepository->find($id);
    }

    public function getOneByName(string $name): ?TicketLabel
    {
        return $this->ticketLabelRepository->findOneBy(['name' => $name]);
    }

    /**
     * @return TicketLabel[]
     */
    public function getAll(): array
    {
        return $this->ticketLabelRepository->findAll();
    }

    public function save(TicketLabel $model): TicketLabel
    {
        $this->ticketLabelRepository->save($model->setUpdatedAt(new DateTime()), true);

        return $model;
    }

    public function delete(TicketLabel $model): void
    {
        $this->ticketLabelRepository->remove($model, true);
    }
}
