<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ORM\Table(name: '`helpdesk_ticket`')]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private int $ticketNo = 0;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $linkToTicket = null;

    #[ORM\Column(nullable: true)]
    private ?int $linkToTicketId = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\ManyToOne(inversedBy: 'assigneeTickets')]
    private ?User $assignee = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $internalNote = null;

    #[ORM\Column]
    private int $timeSpentInMinutes = 0;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TicketLabel $label = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TicketType $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?TicketStatus $status = null;

    /**
     * @var Collection<int, TicketActivity>
     */
    #[ORM\OneToMany(targetEntity: TicketActivity::class, mappedBy: 'ticket')]
    private Collection $ticketActivities;

    /**
     * @var Collection<int, TicketAttachment>
     */
    #[ORM\OneToMany(targetEntity: TicketAttachment::class, mappedBy: 'ticket')]
    private Collection $attachment;

    /**
     * @var Collection<int, TicketComment>
     */
    #[ORM\OneToMany(targetEntity: TicketComment::class, mappedBy: 'ticket')]
    private Collection $ticketComments;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TicketPriority $priority = null;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->ticketActivities = new ArrayCollection();
        $this->attachment = new ArrayCollection();
        $this->ticketComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicketNo(): int
    {
        return $this->ticketNo;
    }

    public function setTicketNo(int $ticketNo): static
    {
        $this->ticketNo = $ticketNo;

        return $this;
    }

    public function getLinkToTicket(): ?string
    {
        return $this->linkToTicket;
    }

    public function setLinkToTicket(?string $linkToTicket): static
    {
        $this->linkToTicket = $linkToTicket;

        return $this;
    }

    public function getLinkToTicketId(): ?int
    {
        return $this->linkToTicketId;
    }

    public function setLinkToTicketId(?int $linkToTicketId): static
    {
        $this->linkToTicketId = $linkToTicketId;

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }

    public function getAssignee(): ?User
    {
        return $this->assignee;
    }

    public function setAssignee(?User $assignee): static
    {
        $this->assignee = $assignee;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getInternalNote(): ?string
    {
        return $this->internalNote;
    }

    public function setInternalNote(?string $internalNote): static
    {
        $this->internalNote = $internalNote;

        return $this;
    }

    public function getTimeSpentInMinutes(): int
    {
        return $this->timeSpentInMinutes;
    }

    public function setTimeSpentInMinutes(int $timeSpentInMinutes): static
    {
        $this->timeSpentInMinutes = $timeSpentInMinutes;

        return $this;
    }

    public function getLabel(): ?TicketLabel
    {
        return $this->label;
    }

    public function setLabel(?TicketLabel $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getType(): ?TicketType
    {
        return $this->type;
    }

    public function setType(?TicketType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): ?TicketStatus
    {
        return $this->status;
    }

    public function setStatus(?TicketStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, TicketActivity>
     */
    public function getTicketActivities(): Collection
    {
        return $this->ticketActivities;
    }

    public function addTicketActivity(TicketActivity $ticketActivity): static
    {
        if (!$this->ticketActivities->contains($ticketActivity)) {
            $this->ticketActivities->add($ticketActivity);
            $ticketActivity->setTicket($this);
        }

        return $this;
    }

    public function removeTicketActivity(TicketActivity $ticketActivity): static
    {
        if ($this->ticketActivities->removeElement($ticketActivity)) {
            // set the owning side to null (unless already changed)
            if ($ticketActivity->getTicket() === $this) {
                $ticketActivity->setTicket(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TicketAttachment>
     */
    public function getAttachment(): Collection
    {
        return $this->attachment;
    }

    public function addAttachment(TicketAttachment $attachment): static
    {
        if (!$this->attachment->contains($attachment)) {
            $this->attachment->add($attachment);
            $attachment->setTicket($this);
        }

        return $this;
    }

    public function removeAttachment(TicketAttachment $attachment): static
    {
        if ($this->attachment->removeElement($attachment)) {
            // set the owning side to null (unless already changed)
            if ($attachment->getTicket() === $this) {
                $attachment->setTicket(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TicketComment>
     */
    public function getTicketComments(): Collection
    {
        return $this->ticketComments;
    }

    public function addTicketComment(TicketComment $ticketComment): static
    {
        if (!$this->ticketComments->contains($ticketComment)) {
            $this->ticketComments->add($ticketComment);
            $ticketComment->setTicket($this);
        }

        return $this;
    }

    public function removeTicketComment(TicketComment $ticketComment): static
    {
        if ($this->ticketComments->removeElement($ticketComment)) {
            // set the owning side to null (unless already changed)
            if ($ticketComment->getTicket() === $this) {
                $ticketComment->setTicket(null);
            }
        }

        return $this;
    }

    public function getPriority(): ?TicketPriority
    {
        return $this->priority;
    }

    public function setPriority(?TicketPriority $priority): static
    {
        $this->priority = $priority;

        return $this;
    }
}
