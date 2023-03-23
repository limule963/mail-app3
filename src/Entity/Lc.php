<?php

namespace App\Entity;

use App\Repository\LcRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LcRepository::class)]
class Lc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sender = null;

    #[ORM\ManyToOne(inversedBy: 'lcs')]
    private ?Compaign $compaign = null;

    #[ORM\ManyToOne(inversedBy: 'lcs')]
    private ?Step $step = null;

    #[ORM\ManyToOne(inversedBy: 'lcs')]
    private ?Lead $lc_lead = null;

    #[ORM\ManyToOne(inversedBy: 'lcs')]
    private ?Dsn $dsn = null;

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

    public function setSender(?string $sender): self
    {
        $this->sender = $sender;

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

    public function getStep(): ?Step
    {
        return $this->step;
    }

    public function setStep(?Step $step): self
    {
        $this->step = $step;

        return $this;
    }

    public function getLcLead(): ?Lead
    {
        return $this->lc_lead;
    }

    public function setLcLead(?Lead $lc_lead): self
    {
        $this->lc_lead = $lc_lead;

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
