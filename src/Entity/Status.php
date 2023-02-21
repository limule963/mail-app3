<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Compaign::class)]
    private Collection $compaigns;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Lead::class)]
    private Collection $leads;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Step::class)]
    private Collection $steps;

    public function __construct()
    {
        $this->compaigns = new ArrayCollection();
        $this->leads = new ArrayCollection();
        $this->steps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Compaign>
     */
    public function getCompaigns(): Collection
    {
        return $this->compaigns;
    }

    public function addCompaign(Compaign $compaign): self
    {
        if (!$this->compaigns->contains($compaign)) {
            $this->compaigns->add($compaign);
            $compaign->setStatus($this);
        }

        return $this;
    }

    public function removeCompaign(Compaign $compaign): self
    {
        if ($this->compaigns->removeElement($compaign)) {
            // set the owning side to null (unless already changed)
            if ($compaign->getStatus() === $this) {
                $compaign->setStatus(null);
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
            $lead->setStatus($this);
        }

        return $this;
    }

    public function removeLead(Lead $lead): self
    {
        if ($this->leads->removeElement($lead)) {
            // set the owning side to null (unless already changed)
            if ($lead->getStatus() === $this) {
                $lead->setStatus(null);
            }
        }

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
            $step->setStatus($this);
        }

        return $this;
    }

    public function removeStep(Step $step): self
    {
        if ($this->steps->removeElement($step)) {
            // set the owning side to null (unless already changed)
            if ($step->getStatus() === $this) {
                $step->setStatus(null);
            }
        }

        return $this;
    }
}
