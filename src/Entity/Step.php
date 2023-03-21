<?php

namespace App\Entity;

use App\AppMailer\Data\STATUS as STAT;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\StepRepository;

#[ORM\Entity(repositoryClass: StepRepository::class)]
class Step
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    public int $dayAfterLastStep = 1;

    // #[ORM\Column]
    // public ?int $startTime = null;


    #[ORM\Column(length: 255)]
    public string $stepState = STAT::STEP_ACTIVE;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Email $email = null;

    #[ORM\ManyToOne(inversedBy: 'steps',cascade:['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'steps')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Compaign $compaign = null;

    #[ORM\Column(nullable: true)]
    private ?int $tms = null;

    #[ORM\Column(nullable: true)]
    private ?int $tmo = null;

    #[ORM\Column(nullable: true)]
    private ?int $tmr = null;

    #[ORM\Column(nullable: true)]
    private ?int $tlc = null;

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

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function setEmail(Email $email): self
    {
        $this->email = $email;

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

    public function getCompaign(): ?Compaign
    {
        return $this->compaign;
    }

    public function setCompaign(?Compaign $compaign): self
    {
        $this->compaign = $compaign;

        return $this;
    }

    public function getDayAfterLastStep(): ?int
    {
        return $this->dayAfterLastStep;
    }

    public function setDayAfterLastStep(int $dayAfterLastStep): self
    {
        $this->dayAfterLastStep = $dayAfterLastStep;

        return $this;
    }



    
    // public function getLeadStatus(): ?string
    // {
    //     return $this->leadStatus;
    // }

    // public function setLeadStatus(string $leadStatus): self
    // {
    //     $this->leadStatus = $leadStatus;

    //     return $this;
    // }

    public function getTms(): ?int
    {
        return $this->tms;
    }

    public function setTms(?int $tms): self
    {
        $this->tms = $tms;

        return $this;
    }

    public function getTmo(): ?int
    {
        return $this->tmo;
    }

    public function setTmo(?int $tmo): self
    {
        $this->tmo = $tmo;

        return $this;
    }

    public function getTmr(): ?int
    {
        return $this->tmr;
    }

    public function setTmr(?int $tmr): self
    {
        $this->tmr = $tmr;

        return $this;
    }

    public function getTlc(): ?int
    {
        return $this->tlc;
    }

    public function setTlc(?int $tlc): self
    {
        $this->tlc = $tlc;

        return $this;
    }
}
