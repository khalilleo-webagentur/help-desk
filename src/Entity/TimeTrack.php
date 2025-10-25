<?php

namespace App\Entity;

use App\Repository\TimeTrackRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: TimeTrackRepository::class)]
#[ORM\Table(name: '`helpdesk_time_track`')]
class TimeTrack
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'timeTracks')]
    private ?Ticket $ticket = null;

    #[ORM\ManyToOne(inversedBy: 'timeTracks')]
    private ?User $user = null;

    #[ORM\Column]
    private int $minutes; // The total time spent on the task

    #[ORM\Column(length: 255)]
    private string $note;

    #[ORM\Column]
    private DateTime $updatedAt;

    #[ORM\Column]
    private DateTime $createdAt;

    public function __construct()
    {
        $this->setCreatedAt(new DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): static
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(null|User|UserInterface $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMinutes(): int
    {
        return $this->minutes;
    }

    public function setMinutes(int $minutes): static
    {
        $this->minutes = $minutes;

        return $this;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
