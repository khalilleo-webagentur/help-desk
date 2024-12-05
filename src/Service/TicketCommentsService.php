<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\TicketComment;
use App\Repository\TicketCommentRepository;

final readonly class TicketCommentsService
{
    public function __construct(
        private TicketCommentRepository $ticketCommentRepository
    ) {
    }

    public function save(TicketComment $ticketComment): TicketComment
    {
        $this->ticketCommentRepository->save($ticketComment->setUpdatedAt(new \DateTime()), true);
        return $ticketComment;
    }
}