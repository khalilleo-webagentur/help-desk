<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Ticket;
use App\Entity\TicketActivity;
use App\Repository\TicketActivityRepository;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class TicketActivitiesService
{
    public function __construct(
        private TicketActivityRepository $ticketActivityRepository
    ) {
    }

    /**
     * @return TicketActivity[]
     */
    public function getAllByTicket(Ticket $ticket): array
    {
        return $this->ticketActivityRepository->findBy(['ticket' => $ticket], ['createdAt' => 'DESC']);
    }

    public function add(Ticket $ticket, ?UserInterface $user, string $message, bool $isHidden = false): void
    {
        $ticketActivity = new TicketActivity();
        $ticketActivity
            ->setTicket($ticket)
            ->setUser($user)
            ->setMessage($message)
            ->setIsHidden($isHidden);
        $this->save($ticketActivity);
    }

    public function save(TicketActivity $model): TicketActivity
    {
        $this->ticketActivityRepository->save($model->setUpdatedAt(new DateTime()), true);

        return $model;
    }

    public function deleteByIssue(Ticket $issue): void
    {
        foreach ($issue->getTicketActivities() as $ticketActivity) {
            $this->delete($ticketActivity);
        }
    }

    public function delete(TicketActivity $model): void
    {
        $this->ticketActivityRepository->remove($model, true);
    }
}