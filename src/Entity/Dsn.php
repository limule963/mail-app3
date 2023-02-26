<?php

namespace App\Entity;

use App\Repository\DsnRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DsnRepository::class)]
class Dsn
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,unique:true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $host = null;

    #[ORM\Column]
    private int $port = 587;

    

    public bool $sendState = false;

    #[ORM\ManyToOne(inversedBy: 'dsns')]
    private ?Compaign $compaign = null;

    #[ORM\ManyToOne(inversedBy: 'dsns')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    // #[ORM\ManyToMany(targetEntity: Compaign::class, mappedBy: 'dsns')]
    // private Collection $compaigns;

    public function __construct()
    {
        // $this->compaigns = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    // /**
    //  * @return Collection<int, Compaign>
    //  */
    // public function getCompaigns(): Collection
    // {
    //     return $this->compaigns;
    // }

    // public function addCompaign(Compaign $compaign): self
    // {
    //     if (!$this->compaigns->contains($compaign)) {
    //         $this->compaigns->add($compaign);
    //         $compaign->addDsn($this);
    //     }

    //     return $this;
    // }

    // public function removeCompaign(Compaign $compaign): self
    // {
    //     if ($this->compaigns->removeElement($compaign)) {
    //         $compaign->removeDsn($this);
    //     }

    //     return $this;
    // }

    public function getDsn():string
    {
        // $dsn =  'smtp://contact@clemaos.com:jC1*rAJ8GGph9@u@mail51.lwspanel.com:587?verify_peer=0';

        $username = urlencode($this->username);
        $password = urlencode($this->password);
        $host = urlencode($this->host);
        $port = $this->port;

        $dsn =  'smtp://'
                . $username.":"
                . $password."@"
                . $host.":"
                . $port.'?verify_peer=1';
                
        return $dsn;
                
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
    


}
