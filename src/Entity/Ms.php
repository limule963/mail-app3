<?php

namespace App\Entity;

use App\Repository\MsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MsRepository::class)]
class Ms
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $sender = null;

    #[ORM\ManyToOne(inversedBy: 'ms')]
    private ?Dsn $dsn = null;

    #[ORM\ManyToOne(inversedBy: 'ms')]
    private ?Lead $ms_lead = null;

    #[ORM\ManyToOne(inversedBy: 'ms')]
    private ?Step $step = null;

    #[ORM\ManyToOne(inversedBy: 'ms')]
    private ?Compaign $compaign = null;


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

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getMsLead(): ?Lead
    {
        return $this->ms_lead;
    }

    public function setMsLead(?Lead $ms_lead): self
    {
        $this->ms_lead = $ms_lead;

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
