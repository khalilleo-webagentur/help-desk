<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Ticket;
use App\Entity\TimeTrack;
use App\Repository\TimeTrackRepository;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class TimeTrackService
{
    public function __construct(
        private TimeTrackRepository $timeTrackRepository,
    ){
    }

    public function getById(int $id): ?TimeTrack
    {
        return $this->timeTrackRepository->find($id);
    }

    public function getOneByTicketAndId(Ticket $ticket, int $id): ?TimeTrack
    {
        return $this->timeTrackRepository->findOneBy(['ticket' => $ticket, 'id' => $id]);
    }

    public function getTotalSpentTime(Ticket $ticket): int
    {
        $totalMinutes = 0;

        foreach ($ticket->getTimeTracks() as $timeTrack) {
            $totalMinutes += $timeTrack->getMinutes();
        }

        return $totalMinutes;
    }

    public function getSpendTimeNotesByTicket(Ticket $ticket): string
    {
        $notes = '';

        foreach ($ticket->getTimeTracks() as $i => $timeTrack) {
            $notes .= $i+1 .') ' . $timeTrack->getNote() . ',' . PHP_EOL;
        }

        return rtrim($notes, ',' . PHP_EOL);
    }

    public function add(UserInterface $user, Ticket $ticket, int $minutes, string $note): TimeTrack
    {
        $timeTrack = new TimeTrack();
        $timeTrack
            ->setUser($user)
            ->setTicket($ticket)
            ->setMinutes($minutes)
            ->setNote($note);

        return $this->save($timeTrack);
    }

    public function store(TimeTrack $timeTrack, int $minutes, string $note): TimeTrack
    {
        $timeTrack
            ->setMinutes($minutes)
            ->setNote($note);

        return $this->save($timeTrack);
    }

    public function save(TimeTrack $timeTrack): TimeTrack
    {
        $this->timeTrackRepository->save($timeTrack->setUpdatedAt(new DateTime()), true);
        return $timeTrack;
    }

    public function delete(TimeTrack $timeTrack, bool $flush): void
    {
        $this->timeTrackRepository->remove($timeTrack, $flush);
    }
}