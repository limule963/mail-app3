<?php

namespace App\Entity;

use App\Repository\MrRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MrRepository::class)]
class Mr
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'mr')]
    private ?Lead $mr_lead = null;

    #[ORM\ManyToOne(inversedBy: 'mr')]
    private ?Step $step = null;

    #[ORM\ManyToOne(inversedBy: 'mr')]
    private ?Compaign $compaign = null;


    #[ORM\ManyToOne(inversedBy: 'mr')]
    private ?Dsn $dsn = null;

    #[ORM\Column(length: 255)]
    private ?string $sender = null;

    public function __construct()
    {
        if($this->date == null ) $this->date = new \DateTimeImmutable();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMrLead(): ?Lead
    {
        return $this->mr_lead;
    }

    public function setMrLead(?Lead $mr_lead): self
    {
        $this->mr_lead = $mr_lead;

        return $this;
    }

    public function getStep(): ?Step
    {
        return $this->step;
    }

    public function setStep(?Step $step): self
    {
        $this->step = $step;

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

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }


    public function setDsn(?Dsn $dsn): self
    {
        $this->dsn = $dsn;

        return $this;
    }

    public function getDsn(): ?Dsn
    {
        return $this->dsn;
    }

}
