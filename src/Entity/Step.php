<?php

namespace App\Entity;

use App\AppMailer\Data\STATUS as STAT;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    public int $dayAfterLastStep = 0;

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

    #[ORM\OneToMany(mappedBy: 'step', targetEntity: Ms::class)]
    private Collection $ms;

    #[ORM\OneToMany(mappedBy: 'step', targetEntity: Mo::class)]
    private Collection $mo;

    #[ORM\OneToMany(mappedBy: 'step', targetEntity: Mr::class)]
    private Collection $mr;

    #[ORM\OneToMany(mappedBy: 'nextStep', targetEntity: Lead::class)]
    private Collection $leads;

    #[ORM\Column(nullable: true)]
    private ?int $stepOrder = null;

    #[ORM\OneToMany(mappedBy: 'step', targetEntity: Lc::class)]
    private Collection $lcs;

    public function __construct()
    {
        $this->ms = new ArrayCollection();
        $this->mo = new ArrayCollection();
        $this->mr = new ArrayCollection();
        $this->leads = new ArrayCollection();
        $this->lcs = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Ms>
     */
    public function getMs(): Collection
    {
        return $this->ms;
    }

    public function addMs(Ms $m): self
    {
        if (!$this->ms->contains($m)) {
            $this->ms->add($m);
            $m->setStep($this);
        }

        return $this;
    }

    public function removeMs(Ms $m): self
    {
        if ($this->ms->removeElement($m)) {
            // set the owning side to null (unless already changed)
            if ($m->getStep() === $this) {
                $m->setStep(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mo>
     */
    public function getMo(): Collection
    {
        return $this->mo;
    }

    public function addMo(Mo $mo): self
    {
        if (!$this->mo->contains($mo)) {
            $this->mo->add($mo);
            $mo->setStep($this);
        }

        return $this;
    }

    public function removeMo(Mo $mo): self
    {
        if ($this->mo->removeElement($mo)) {
            // set the owning side to null (unless already changed)
            if ($mo->getStep() === $this) {
                $mo->setStep(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mr>
     */
    public function getMr(): Collection
    {
        return $this->mr;
    }

    public function addMr(Mr $mr): self
    {
        if (!$this->mr->contains($mr)) {
            $this->mr->add($mr);
            $mr->setStep($this);
        }

        return $this;
    }

    public function removeMr(Mr $mr): self
    {
        if ($this->mr->removeElement($mr)) {
            // set the owning side to null (unless already changed)
            if ($mr->getStep() === $this) {
                $mr->setStep(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lead>
     */
    public function getLeads(): Collection
    {
        return $this->leads;
    }

    public function addLead(Lead $lead): self
    {
        if (!$this->leads->contains($lead)) {
            $this->leads->add($lead);
            $lead->setStep($this);
        }

        return $this;
    }

    public function removeLead(Lead $lead): self
    {
        if ($this->leads->removeElement($lead)) {
            // set the owning side to null (unless already changed)
            if ($lead->getStep() === $this) {
                $lead->setStep(null);
            }
        }

        return $this;
    }

    public function getStepOrder(): ?int
    {
        return $this->stepOrder;
    }

    public function setStepOrder(?int $stepOrder): self
    {
        $this->stepOrder = $stepOrder;

        return $this;
    }

    /**
     * @return Collection<int, Lc>
     */
    public function getLcs(): Collection
    {
        return $this->lcs;
    }

    public function addLc(Lc $lc): self
    {
        if (!$this->lcs->contains($lc)) {
            $this->lcs->add($lc);
            $lc->setStep($this);
        }

        return $this;
    }

    public function removeLc(Lc $lc): self
    {
        if ($this->lcs->removeElement($lc)) {
            // set the owning side to null (unless already changed)
            if ($lc->getStep() === $this) {
                $lc->setStep(null);
            }
        }

        return $this;
    }
}
