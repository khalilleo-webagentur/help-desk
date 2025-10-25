<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Ticket;
use App\Entity\TicketComment;
use App\Repository\TicketCommentRepository;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class TicketCommentsService
{
    public function __construct(
        private TicketCommentRepository $ticketCommentRepository
    ) {
    }

    public function getById(int $id): ?TicketComment
    {
        return $this->ticketCommentRepository->find($id);
    }

    public function getByTicket(Ticket$ticket): ?TicketComment
    {
        return $this->ticketCommentRepository->findOneBy(['ticket' => $ticket]);
    }

    /**
     * @return TicketComment[]
     */
    public function getAllByTicket(Ticket $ticket): array
    {
        return $this->ticketCommentRepository->findBy(['ticket' => $ticket]);
    }

    public function save(TicketComment $ticketComment): TicketComment
    {
        $this->ticketCommentRepository->save($ticketComment->setUpdatedAt(new \DateTime()), true);
        return $ticketComment;
    }

    public function deleteAllByTicket(Ticket $ticket, bool $flush): void
    {
        foreach ($this->getAllByTicket($ticket) as $ticketComment) {
            $this->delete($ticketComment, $flush);
        }
    }

    public function deleteAllByUserAndTicketComment(UserInterface $user, TicketComment $ticketComment): void
    {
        $ticketComment->removeCommentedBy($user);
        $this->ticketCommentRepository->remove($ticketComment, true);
    }

    public function delete(TicketComment $ticketComment, bool $flush): void
    {
        $this->ticketCommentRepository->remove($ticketComment, $flush);
    }
}