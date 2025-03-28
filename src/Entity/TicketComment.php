<?php

namespace App\Entity;

use App\Repository\TicketCommentRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: TicketCommentRepository::class)]
#[ORM\Table(name: '`helpdesk_ticket_comment`')]
class TicketComment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ticketComments')]
    private ?Ticket $ticket = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $description = "";

    #[ORM\Column]
    private bool $isSeen = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $seenAt = null;

    #[ORM\Column]
    private int $likeCounter = 0;

    #[ORM\Column]
    private int $disLikeCounter = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $createdAt = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'ticketComments')]
    private Collection $commentedBy;

    public function __construct()
    {
        $this->setCreatedAt(new DateTime());
        $this->commentedBy = new ArrayCollection();
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isSeen(): bool
    {
        return $this->isSeen;
    }

    public function setSeen(bool $isSeen): static
    {
        $this->isSeen = $isSeen;

        return $this;
    }

    public function getSeenAt(): ?DateTimeInterface
    {
        return $this->seenAt;
    }

    public function setSeenAt(?DateTimeInterface $seenAt): static
    {
        $this->seenAt = $seenAt;

        return $this;
    }

    public function getLikeCounter(): int
    {
        return $this->likeCounter;
    }

    public function setLikeCounter(int $likeCounter): static
    {
        $this->likeCounter = $likeCounter;

        return $this;
    }

    public function getDisLikeCounter(): int
    {
        return $this->disLikeCounter;
    }

    public function setDisLikeCounter(int $disLikeCounter): static
    {
        $this->disLikeCounter = $disLikeCounter;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, User|UserInterface>
     */
    public function getCommentedBy(): Collection
    {
        return $this->commentedBy;
    }

    public function addCommentedBy(User|UserInterface $commentedBy): static
    {
        if (!$this->commentedBy->contains($commentedBy)) {
            $this->commentedBy->add($commentedBy);
        }

        return $this;
    }

    public function removeCommentedBy(User|UserInterface $commentedBy): static
    {
        $this->commentedBy->removeElement($commentedBy);

        return $this;
    }
}
