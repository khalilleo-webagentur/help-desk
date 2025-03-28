<?php

namespace App\Entity;

use App\Helper\AppHelper;
use App\Repository\UserSettingRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSettingRepository::class)]
#[ORM\Table(name: '`helpdesk_user_setting`')]
class UserSetting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userSettings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // @TODO rename it to notifyOnTickedResolved
    #[ORM\Column]
    private bool $notifyCloseTicket = AppHelper::NOTIFY_CUSTOMER_ON_TICKET_RESOLVED;

    // @TODO rename it to notifyOnTickedCreated
    #[ORM\Column]
    private bool $notifyNewTicket = AppHelper::NOTIFY_WEBMASTER_ON_TICKET_CREATED;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->setCreatedAt(new DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function notifyCloseTicket(): bool
    {
        return $this->notifyCloseTicket;
    }

    public function setNotifyCloseTicket(bool $notifyCloseTicket): static
    {
        $this->notifyCloseTicket = $notifyCloseTicket;

        return $this;
    }

    public function notifyNewTicket(): bool
    {
        return $this->notifyNewTicket;
    }

    public function setNotifyNewTicket(bool $notifyNewTicket): static
    {
        $this->notifyNewTicket = $notifyNewTicket;

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
}
