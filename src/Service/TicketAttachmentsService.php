<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Ticket;
use App\Entity\TicketAttachment;
use App\Repository\TicketAttachmentRepository;

final readonly class TicketAttachmentsService
{
    public function __construct(
        private TicketAttachmentRepository $ticketAttachmentRepository,
    ){
    }

    public function getOneByTicket(Ticket $ticket): ?TicketAttachment
    {
        return $this->ticketAttachmentRepository->findOneBy(['ticket' => $ticket]);
    }

    /**
     * @return TicketAttachment[]
     */
    public function getAllByTicket(Ticket $ticket): array
    {
        return $this->ticketAttachmentRepository->findBy(['ticket' => $ticket]);
    }

    public function create(Ticket $ticket, string $filename, int $size, string $extension): TicketAttachment
    {
        $ticketAttachment = new TicketAttachment();
        $ticketAttachment
            ->setTicket($ticket)
            ->setFilename($filename)
            ->setSize($size)
            ->setExtension($extension);
        $this->save($ticketAttachment);

        return $ticketAttachment;
    }

    public function save(TicketAttachment $ticketAttachment): TicketAttachment
    {
        $this->ticketAttachmentRepository->save(
            $ticketAttachment->setUpdatedAt(new \DateTime()),
            true
        );
        return $ticketAttachment;
    }
}