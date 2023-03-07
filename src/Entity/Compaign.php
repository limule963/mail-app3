<?php

namespace App\Entity;

use App\Repository\CompaignRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompaignRepository::class)]
class Compaign
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,unique:true)]
    private ?string $name = null;
    
    #[ORM\Column]
    public ?bool $newStepPriority = true;

    #[ORM\ManyToOne(inversedBy: 'compaigns')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'compaign', targetEntity: Lead::class,cascade:['persist','remove'])]
    private Collection $leads;

    #[ORM\ManyToOne(inversedBy: 'compaigns',cascade:["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\OneToMany(mappedBy: 'compaign', targetEntity: Step::class,cascade:["persist",'remove'])]
    private Collection $steps;

    // #[ORM\ManyToMany(targetEntity: Dsn::class, inversedBy: 'compaigns',cascade:['persist'])]
    // private Collection $dsns;

    // #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    // private ?Schedule $schedule = null;

    #[ORM\OneToMany(mappedBy: 'compaign', targetEntity: Dsn::class,cascade:['persist'])]
    private Collection $dsns;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Schedule $schedule = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column]
    private bool $isTracker = true;

    // #[ORM\OneToOne(inversedBy: "compaign", cascade: ['persist', 'remove'])]
    // private ?Schedule $schedule = null;

    public function __construct()
    {
        $this->leads = new ArrayCollection();
        $this->steps = new ArrayCollection();
        // $this->dsns = new ArrayCollection();
        if($this->createAt == '') $this->createAt = new DateTimeImmutable();
        
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
            $lead->setCompaign($this);
        }

        return $this;
    }
    public function addLeads(array $leads): self
    {
        foreach($leads as $lead)
        {
            $this->addLead($lead);
        } 

        return $this;
    }

    public function removeLead(Lead $lead): self
    {
        if ($this->leads->removeElement($lead)) {
            // set the owning side to null (unless already changed)
            if ($lead->getCompaign() === $this) {
                $lead->setCompaign(null);
            }
        }

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

    /**
     * @return Collection<int, Step>
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(Step $step): self
    {
        if (!$this->steps->contains($step)) {
            $this->steps->add($step);
            $step->setCompaign($this);
        }

        return $this;
    }

    public function removeStep(Step $step): self
    {
        if ($this->steps->removeElement($step)) {
            // set the owning side to null (unless already changed)
            if ($step->getCompaign() === $this) {
                $step->setCompaign(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection<int, Dsn>
    //  */
    // public function getDsns(): Collection
    // {
    //     return $this->dsns;
    // }

    // public function addDsn(Dsn $dsn): self
    // {
    //     if (!$this->dsns->contains($dsn)) {
    //         $this->dsns->add($dsn);
    //     }

    //     return $this;
    // }

    // public function addDsns($dsns)
    // {
    //     foreach($dsns as $dsn) $this->addDsn($dsn);
    // }

    // public function removeDsn(Dsn $dsn): self
    // {
    //     $this->dsns->removeElement($dsn);

    //     return $this;
    // }

    // public function getSchedule(): ?Schedule
    // {
    //     return $this->schedule;
    // }

    // public function setSchedule(?Schedule $schedule): self
    // {
    //     $this->schedule = $schedule;

    //     return $this;
    // }

    public function isNewStepPriority(): ?bool
    {
        return $this->newStepPriority;
    }

    public function setNewStepPriority(bool $newStepPriority): self
    {
        $this->newStepPriority = $newStepPriority;

        return $this;
    }

    // public function getSchedule(): ?Schedule
    // {
    //     return $this->schedule;
    // }

    // public function setSchedule(?Schedule $schedule): self
    // {
    //     $this->schedule = $schedule;

    //     return $this;
    // }

    /**
     * @return Collection<int, Dsn>
     */
    public function getDsns(): Collection
    {
        return $this->dsns;
    }

    public function addDsn(Dsn $dsn): self
    {
        if (!$this->dsns->contains($dsn)) {
            $this->dsns->add($dsn);
            $dsn->setCompaign($this);
        }

        return $this;
    }
    public function addDsns(array $dsns): self
    {
        foreach($dsns as $dsn) $this->addDsn($dsn);

        return $this;
    }

    public function removeDsn(Dsn $dsn): self
    {
        if ($this->dsns->removeElement($dsn)) {
            // set the owning side to null (unless already changed)
            if ($dsn->getCompaign() === $this) {
                $dsn->setCompaign(null);
            }
        }

        return $this;
    }

    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    public function setSchedule(?Schedule $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function isIsTracker(): ?bool
    {
        return $this->isTracker;
    }

    public function setIsTracker(bool $isTracker): self
    {
        $this->isTracker = $isTracker;

        return $this;
    }
}
