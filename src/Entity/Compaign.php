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

    #[ORM\Column(length: 255)]
    private ?string $name = null;
    
    #[ORM\Column]
    public ?bool $newStepPriority = false;

    #[ORM\ManyToOne(inversedBy: 'compaigns')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'compaign', targetEntity: Lead::class,cascade:['persist','remove'],orphanRemoval:true)]
    private Collection $leads;

    #[ORM\ManyToOne(inversedBy: 'compaigns',cascade:["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\OneToMany(mappedBy: 'compaign', targetEntity: Step::class,cascade:["persist",'remove'],orphanRemoval:true)]
    private Collection $steps;



    // #[ORM\OneToMany(mappedBy: 'compaign', targetEntity: Dsn::class,cascade:['persist'])]
    // private Collection $dsns;

    #[ORM\OneToOne(cascade: ['persist', 'remove'],orphanRemoval: true)]
    private ?Schedule $schedule = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column]
    private bool $isTracker = true;

    #[ORM\ManyToMany(targetEntity: Dsn::class, inversedBy: 'compaigns')]
    private Collection $dsns;

    // #[ORM\Column(nullable: true)]
    // private ?int $tms = null;

    // #[ORM\Column(nullable: true)]
    // private ?int $tmo = null;

    // #[ORM\Column(nullable: true)]
    // private ?int $tmr = null;

    // #[ORM\Column(nullable: true)]
    // private ?int $tlc = null;

    #[ORM\OneToMany(mappedBy: 'compaign', targetEntity: Ms::class)]
    private Collection $ms;

    #[ORM\OneToMany(mappedBy: 'compaign', targetEntity: Mo::class)]
    private Collection $mo;

    #[ORM\OneToMany(mappedBy: 'compaign', targetEntity: Mr::class)]
    private Collection $mr;

    #[ORM\OneToMany(mappedBy: 'compaign', targetEntity: Lc::class)]
    private Collection $lcs;

    // #[ORM\OneToOne(inversedBy: "compaign", cascade: ['persist', 'remove'])]
    // private ?Schedule $schedule = null;

    public function __construct()
    {
        $this->leads = new ArrayCollection();
        $this->steps = new ArrayCollection();
        // $this->dsns = new ArrayCollection();
        if($this->createAt == '') $this->createAt = new DateTimeImmutable();
        $this->ms = new ArrayCollection();
        $this->mo = new ArrayCollection();
        $this->mr = new ArrayCollection();
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

    public function addUniqLead(Lead $lead)
    {
        $email = $lead->getEmailAddress();

        /**@param Lead $element */
        if(!$this->leads->exists(function($key,$element) use($email){
            return $element->getEmailAddress() == $email;
        }))
        {
            $this->leads->add($lead);
            $lead->setCompaign($this);
        
            
        }
      
    }

    /**@param Lead[] $leads */
    public function addUniqLeads($leads)
    {

        foreach($leads as $lead)
        {
            $this->addUniqLead($lead);
        }
        
    }
    

    public function addLead(Lead $lead): self
    {
        if (!$this->leads->contains($lead)) {
            $this->leads->add($lead);
            $lead->setCompaign($this);
        }

        return $this;
    }
    /**@param Lead[] $leads */
    public function addLeads($leads): self
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


    public function isNewStepPriority(): ?bool
    {
        return $this->newStepPriority;
    }

    public function setNewStepPriority(bool $newStepPriority): self
    {
        $this->newStepPriority = $newStepPriority;

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
    //         $dsn->setCompaign($this);
    //     }

    //     return $this;
    // }
    // public function addDsns(array $dsns): self
    // {
    //     foreach($dsns as $dsn) $this->addDsn($dsn);

    //     return $this;
    // }

    // public function removeDsn(Dsn $dsn): self
    // {
    //     if ($this->dsns->removeElement($dsn)) {
    //         // set the owning side to null (unless already changed)
    //         if ($dsn->getCompaign() === $this) {
    //             $dsn->setCompaign(null);
    //         }
    //     }

    //     return $this;
    // }

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
        }

        return $this;
    }

    public function removeDsn(Dsn $dsn): self
    {
        $this->dsns->removeElement($dsn);

        return $this;
    }

    // public function getTms(): ?int
    // {
    //     return $this->tms;
    // }

    // public function setTms(?int $tms): self
    // {
    //     $this->tms = $tms;

    //     return $this;
    // }

    // public function getTmo(): ?int
    // {
    //     return $this->tmo;
    // }

    // public function setTmo(?int $tmo): self
    // {
    //     $this->tmo = $tmo;

    //     return $this;
    // }

    // public function getTmr(): ?int
    // {
    //     return $this->tmr;
    // }

    // public function setTmr(?int $tmr): self
    // {
    //     $this->tmr = $tmr;

    //     return $this;
    // }

    // public function getTlc(): ?int
    // {
    //     return $this->tlc;
    // }

    // public function setTlc(?int $tlc): self
    // {
    //     $this->tlc = $tlc;

    //     return $this;
    // }

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
            $m->setCompaign($this);
        }

        return $this;
    }

    public function removeMs(Ms $m): self
    {
        if ($this->ms->removeElement($m)) {
            // set the owning side to null (unless already changed)
            if ($m->getCompaign() === $this) {
                $m->setCompaign(null);
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
            $mo->setCompaign($this);
        }

        return $this;
    }

    public function removeMo(Mo $mo): self
    {
        if ($this->mo->removeElement($mo)) {
            // set the owning side to null (unless already changed)
            if ($mo->getCompaign() === $this) {
                $mo->setCompaign(null);
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
            $mr->setCompaign($this);
        }

        return $this;
    }

    public function removeMr(Mr $mr): self
    {
        if ($this->mr->removeElement($mr)) {
            // set the owning side to null (unless already changed)
            if ($mr->getCompaign() === $this) {
                $mr->setCompaign(null);
            }
        }

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
            $lc->setCompaign($this);
        }

        return $this;
    }

    public function removeLc(Lc $lc): self
    {
        if ($this->lcs->removeElement($lc)) {
            // set the owning side to null (unless already changed)
            if ($lc->getCompaign() === $this) {
                $lc->setCompaign(null);
            }
        }

        return $this;
    }
}
