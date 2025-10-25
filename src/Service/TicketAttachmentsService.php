<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Ticket;
use App\Entity\TicketAttachment;
use App\Entity\User;
use App\Helper\AppHelper;
use App\Repository\TicketAttachmentRepository;
use DateTime;
use DateTimeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class TicketAttachmentsService
{
    public function __construct(
        private TicketAttachmentRepository $ticketAttachmentRepository,
        public DeletableFilesService       $deletableFilesService,
    ) {
    }

    public function getOneByTicketAndId(Ticket $ticket, int $id): ?TicketAttachment
    {
        return $this->ticketAttachmentRepository->findOneBy(['ticket' => $ticket, 'id' => $id]);
    }

    /**
     * @return TicketAttachment[]
     */
    public function getAllByTicket(Ticket $ticket): array
    {
        return $this->ticketAttachmentRepository->findBy(['ticket' => $ticket]);
    }

    public function create(
        UserInterface|User $user,
        Ticket             $ticket,
        string             $originalFilename,
        string             $filename,
        int                $size,
        string             $extension
    ): TicketAttachment
    {
        $ticketAttachment = new TicketAttachment();
        $ticketAttachment
            ->setUser($user)
            ->setFileNo(uniqid())
            ->setTicket($ticket)
            ->setOriginalFileName($originalFilename)
            ->setFilename($filename)
            ->setSize($size)
            ->setExtension($extension);

        $this->save($ticketAttachment);

        return $ticketAttachment;
    }

    public function save(TicketAttachment $ticketAttachment): TicketAttachment
    {
        $this->ticketAttachmentRepository->save(
            $ticketAttachment->setUpdatedAt(new DateTime()),
            true
        );
        return $ticketAttachment;
    }

    /**
     * @return TicketAttachment[]
     */
    public function getAllByCriteria(
        ?string           $fileName,
        ?int              $userId,
        ?string           $fileNo,
        ?string           $type,
        DateTimeInterface $dateFrom,
        DateTimeInterface $dateTo
    ): array
    {
        return $this->ticketAttachmentRepository->findAllByCriteria(
            $fileName,
            $userId,
            $fileNo,
            $type,
            $dateFrom,
            $dateTo
        );
    }

    public function getAllFileNamesByTicket(Ticket $ticket): string
    {
        $filenames = '';

        foreach ($this->getAllByTicket($ticket) as $attachment) {
            $filenames .= $attachment->getFilename() . ',';
        }

        return $filenames;
    }

    public function deleteAllByTicket(Ticket $ticket, bool $flush): void
    {
        foreach ($this->getAllByTicket($ticket) as $ticketAttachment) {
            $this->delete($ticketAttachment, $flush);
        }
    }

    public function delete(TicketAttachment $ticketAttachment, bool $flush): void
    {
        if ($flush) {
            $this->deletableFilesService->add(
                AppHelper::TICKET_ATTACHMENT,
                $ticketAttachment->getFilename()
            );
        }

        $this->ticketAttachmentRepository->remove($ticketAttachment, $flush);
    }
}