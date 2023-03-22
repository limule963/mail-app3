<?php

namespace App\Entity;

use App\Repository\LeadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(mappedBy: 'mailLead', targetEntity: Mail::class, orphanRemoval: true,cascade:['persist','remove'])]
    private Collection $mail;

    #[ORM\OneToMany(mappedBy: 'ms_lead', targetEntity: Ms::class,orphanRemoval:true)]
    private Collection $ms;

    #[ORM\OneToMany(mappedBy: 'mo_lead', targetEntity: Mo::class,orphanRemoval:true)]
    private Collection $mo;

    #[ORM\OneToMany(mappedBy: 'mr_lead', targetEntity: Mr::class,orphanRemoval:true)]
    private Collection $mr;

    #[ORM\ManyToOne]
    private ?Step $step = null;

    #[ORM\ManyToOne(inversedBy: 'leads')]
    private ?Step $nextStep = null;

    #[ORM\OneToMany(mappedBy: 'lc_lead', targetEntity: Lc::class)]
    private Collection $lcs;

    public function __construct()
    {
        $this->mail = new ArrayCollection();
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

    /**
     * @return Collection<int, Mail>
     */
    public function getMail(): Collection
    {
        return $this->mail;
    }

    public function addUniqMail(Mail $mail)
    {
        $mailId = $mail->getMailId();

        /**@param Mail $element */
        if(!$this->mail->exists(function($key,$element) use($mailId){
            return $element->getMailId() == $mailId;
        }))
        {
            $this->mail->add($mail);
            $mail->setMailLead($this);
        }
    }
    

    public function addMail(Mail $mail): self
    {
        if (!$this->mail->contains($mail)) {
            $this->mail->add($mail);
            $mail->setMailLead($this);
        }

        return $this;
    }
    /**@param Mail[] */
    public function addMails(array $mails): self
    {
        foreach($mails as $mail)
        {
            $this->addMail($mail);
        }
        return $this;
    }

    public function removeMail(Mail $mail): self
    {
        if ($this->mail->removeElement($mail)) {
            // set the owning side to null (unless already changed)
            if ($mail->getMailLead() === $this) {
                $mail->setMailLead(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ms>
     */
    public function getMs(): Collection
    {
        return $this->ms;
    }

    public function addM(Ms $m): self
    {
        if (!$this->ms->contains($m)) {
            $this->ms->add($m);
            $m->setMsLead($this);
        }

        return $this;
    }

    public function removeM(Ms $m): self
    {
        if ($this->ms->removeElement($m)) {
            // set the owning side to null (unless already changed)
            if ($m->getMsLead() === $this) {
                $m->setMsLead(null);
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
            $mo->setMoLead($this);
        }

        return $this;
    }

    public function removeMo(Mo $mo): self
    {
        if ($this->mo->removeElement($mo)) {
            // set the owning side to null (unless already changed)
            if ($mo->getMoLead() === $this) {
                $mo->setMoLead(null);
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
            $mr->setMrLead($this);
        }

        return $this;
    }

    public function removeMr(Mr $mr): self
    {
        if ($this->mr->removeElement($mr)) {
            // set the owning side to null (unless already changed)
            if ($mr->getMrLead() === $this) {
                $mr->setMrLead(null);
            }
        }

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

    public function getNextStep(): ?Step
    {
        return $this->nextStep;
    }

    public function setNextStep(?Step $nextStep): self
    {
        $this->nextStep = $nextStep;

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
            $lc->setLcLead($this);
        }

        return $this;
    }

    public function removeLc(Lc $lc): self
    {
        if ($this->lcs->removeElement($lc)) {
            // set the owning side to null (unless already changed)
            if ($lc->getLcLead() === $this) {
                $lc->setLcLead(null);
            }
        }

        return $this;
    }
}
