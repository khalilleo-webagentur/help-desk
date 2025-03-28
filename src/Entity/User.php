<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`helpdesk_user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $token = null;

    #[ORM\Column]
    private bool $ninja = false;

    #[ORM\Column]
    private bool $isTeamLeader = false;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column]
    private bool $isDeleted = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $createdAt = null;

    /**
     * @var Collection<int, UserSetting>
     */
    #[ORM\OneToMany(targetEntity: UserSetting::class, mappedBy: 'user', orphanRemoval: false)]
    private Collection $userSettings;

    /**
     * @var Collection<int, TempUser>
     */
    #[ORM\OneToMany(targetEntity: TempUser::class, mappedBy: 'user')]
    private Collection $tempUsers;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'customer', orphanRemoval: false)]
    private Collection $tickets;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'assignee')]
    private Collection $assigneeTickets;

    /**
     * @var Collection<int, TicketActivity>
     */
    #[ORM\OneToMany(targetEntity: TicketActivity::class, mappedBy: 'user')]
    private Collection $ticketActivities;

    /**
     * @var Collection<int, TicketComment>
     */
    #[ORM\ManyToMany(targetEntity: TicketComment::class, mappedBy: 'commentedBy')]
    private Collection $ticketComments;

    /**
     * @var Collection<int, TicketAttachment>
     */
    #[ORM\OneToMany(targetEntity: TicketAttachment::class, mappedBy: 'user')]
    private Collection $ticketAttachments;

    #[ORM\ManyToOne(inversedBy: 'user')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    public function __construct()
    {
        $this->setCreatedAt(new DateTime());
        $this->userSettings = new ArrayCollection();
        $this->tempUsers = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->assigneeTickets = new ArrayCollection();
        $this->ticketActivities = new ArrayCollection();
        $this->ticketComments = new ArrayCollection();
        $this->ticketAttachments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function isNinja(): bool
    {
        return $this->ninja;
    }

    public function setNinja(bool $ninja): static
    {
        $this->ninja = $ninja;

        return $this;
    }

    public function isTeamLeader(): bool
    {
        return $this->isTeamLeader;
    }

    public function setTeamLeader(bool $isTeamLeader): static
    {
        $this->isTeamLeader = $isTeamLeader;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setDeleted(bool $isDeleted): static
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        //
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
     * @return Collection<int, UserSetting>
     */
    public function getUserSettings(): Collection
    {
        return $this->userSettings;
    }

    public function addUserSetting(UserSetting $userSetting): static
    {
        if (!$this->userSettings->contains($userSetting)) {
            $this->userSettings->add($userSetting);
            $userSetting->setUser($this);
        }

        return $this;
    }

    public function removeUserSetting(UserSetting $userSetting): static
    {
        if ($this->userSettings->removeElement($userSetting)) {
            // set the owning side to null (unless already changed)
            if ($userSetting->getUser() === $this) {
                $userSetting->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TempUser>
     */
    public function getTempUsers(): Collection
    {
        return $this->tempUsers;
    }

    public function addTempUser(TempUser $tempUser): static
    {
        if (!$this->tempUsers->contains($tempUser)) {
            $this->tempUsers->add($tempUser);
            $tempUser->setUser($this);
        }

        return $this;
    }

    public function removeTempUser(TempUser $tempUser): static
    {
        if ($this->tempUsers->removeElement($tempUser)) {
            // set the owning side to null (unless already changed)
            if ($tempUser->getUser() === $this) {
                $tempUser->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setCustomer($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getCustomer() === $this) {
                $ticket->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getAssigneeTickets(): Collection
    {
        return $this->assigneeTickets;
    }

    public function addAssigneeTicket(Ticket $assigneeTicket): static
    {
        if (!$this->assigneeTickets->contains($assigneeTicket)) {
            $this->assigneeTickets->add($assigneeTicket);
            $assigneeTicket->setAssignee($this);
        }

        return $this;
    }

    public function removeAssigneeTicket(Ticket $assigneeTicket): static
    {
        if ($this->assigneeTickets->removeElement($assigneeTicket)) {
            // set the owning side to null (unless already changed)
            if ($assigneeTicket->getAssignee() === $this) {
                $assigneeTicket->setAssignee(null);
            }
        }

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
            $ticketActivity->setUser($this);
        }

        return $this;
    }

    public function removeTicketActivity(TicketActivity $ticketActivity): static
    {
        if ($this->ticketActivities->removeElement($ticketActivity)) {
            // set the owning side to null (unless already changed)
            if ($ticketActivity->getUser() === $this) {
                $ticketActivity->setUser(null);
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
            $ticketComment->addCommentedBy($this);
        }

        return $this;
    }

    public function removeTicketComment(TicketComment $ticketComment): static
    {
        if ($this->ticketComments->removeElement($ticketComment)) {
            $ticketComment->removeCommentedBy($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, TicketAttachment>
     */
    public function getTicketAttachments(): Collection
    {
        return $this->ticketAttachments;
    }

    public function addTicketAttachment(TicketAttachment $ticketAttachment): static
    {
        if (!$this->ticketAttachments->contains($ticketAttachment)) {
            $this->ticketAttachments->add($ticketAttachment);
            $ticketAttachment->setUser($this);
        }

        return $this;
    }

    public function removeTicketAttachment(TicketAttachment $ticketAttachment): static
    {
        if ($this->ticketAttachments->removeElement($ticketAttachment)) {
            // set the owning side to null (unless already changed)
            if ($ticketAttachment->getUser() === $this) {
                $ticketAttachment->setUser(null);
            }
        }

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }
}
