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

    #[ORM\Column(length: 255,unique:true)]
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

    public function __construct()
    {
        $this->mail = new ArrayCollection();
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
}
