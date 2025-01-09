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

    public function save(TicketComment $ticketComment): TicketComment
    {
        $this->ticketCommentRepository->save($ticketComment->setUpdatedAt(new \DateTime()), true);
        return $ticketComment;
    }

    public function delete(UserInterface $user, TicketComment $ticketComment): void
    {
        $ticketComment->removeCommentedBy($user);
        $this->ticketCommentRepository->remove($ticketComment, true);
    }
}