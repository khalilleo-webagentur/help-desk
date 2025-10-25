<?php

namespace App\Entity;

use App\Repository\DeletableFileRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeletableFileRepository::class)]
#[ORM\Table(name: '`helpdesk_deletable_file`')]
class DeletableFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private string $directory; // config parameter name ex: attachments

    #[ORM\Column(length: 255)]
    private string $filename;

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

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function setDirectory(string $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

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
