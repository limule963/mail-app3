<?php

namespace App\Entity;

use App\Repository\MailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MailRepository::class)]
class Mail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $mailId = null;

    #[ORM\Column(length: 255)]
    private ?string $folder = null;

    #[ORM\ManyToOne(inversedBy: 'mails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dsn $dsn = null;

    #[ORM\ManyToOne(inversedBy: 'mail')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lead $mailLead = null;

    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    #[ORM\Column(length: 255)]
    private ?string $fromAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $toAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMailId(): ?int
    {
        return $this->mailId;
    }

    public function setMailId(int $mailId): self
    {
        $this->mailId = $mailId;

        return $this;
    }

    public function getFolder(): ?string
    {
        return $this->folder;
    }

    public function setFolder(string $folder): self
    {
        $this->folder = $folder;

        return $this;
    }

    public function getDsn(): ?Dsn
    {
        return $this->dsn;
    }

    public function setDsn(?Dsn $dsn): self
    {
        $this->dsn = $dsn;

        return $this;
    }

    public function getMailLead(): ?Lead
    {
        return $this->mailLead;
    }

    public function setMailLead(?Lead $mailLead): self
    {
        $this->mailLead = $mailLead;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getFromAddress(): ?string
    {
        return $this->fromAddress;
    }

    public function setFromAddress(?string $fromAddress): self
    {
        $this->fromAddress = $fromAddress;

        return $this;
    }

    public function getToAddress(): ?string
    {
        return $this->toAddress;
    }

    public function setToAddress(?string $toAddress): self
    {
        $this->toAddress = $toAddress;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): self
    {
        $this->date = $date;

        return $this;
    }
}
