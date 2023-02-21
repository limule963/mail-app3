<?php

namespace App\Entity;

use App\Repository\LeadRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeadRepository::class)]
#[ORM\Table(name: '`lead`')]
class Lead
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $emailAddress = null;

    #[ORM\ManyToOne(inversedBy: 'leads')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Compaign $compaign = null;

    #[ORM\ManyToOne(inversedBy: 'leads',cascade:['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sender = null;

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): self
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function getCompaign(): ?Compaign
    {
        return $this->compaign;
    }

    public function setCompaign(?Compaign $compaign): self
    {
        $this->compaign = $compaign;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(?string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }
}
