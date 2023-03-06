<?php

namespace App\Entity;

use App\Repository\DsnRepository;
use DateTimeImmutable;
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

    #[ORM\Column(length: 255)]
    private ?string $username2 = null;

    #[ORM\Column(length: 255)]
    private ?string $password2 = null;

    #[ORM\Column(length: 255)]
    private ?string $host2 = null;

    #[ORM\Column(length: 255)]
    private int $port2 = 993;



    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\OneToMany(mappedBy: 'dsn', targetEntity: Mail::class,cascade:['persist','remove'],orphanRemoval:true)]
    private Collection $mails;

    // #[ORM\ManyToMany(targetEntity: Compaign::class, mappedBy: 'dsns')]
    // private Collection $compaigns;

    public function __construct()
    {
        // $this->compaigns = new ArrayCollection()
        if($this->createAt == '') $this->createAt = new DateTimeImmutable();
        $this->mails = new ArrayCollection();


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

    public function getConnexion():array
    {
        return [

            $this->getConnexionName() =>[
                    
                'mailbox'=>"{".$this->host2.":".$this->port2."/imap/ssl}" ,
                'username'=>$this->username2,
                'password'=>$this->password2,
                'attachments_dir'=> "../var/imap/attachments",
                'server_encoding'=> "UTF-8",
                'create_attachments_dir_if_not_exists'=>true, // default true
                'created_attachments_dir_permissions'=> 777  // default 770
            ]
        ];
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

    public function getUsername2(): ?string
    {
        return $this->username2;
    }

    public function setUsername2(string $username2): self
    {
        $this->username2 = $username2;

        return $this;
    }

    public function getPassword2(): ?string
    {
        return $this->password2;
    }

    public function setPassword2(string $password2): self
    {
        $this->password2 = $password2;

        return $this;
    }

    public function getHost2(): ?string
    {
        return $this->host2;
    }

    public function setHost2(string $host2): self
    {
        $this->host2 = $host2;

        return $this;
    }

    public function getPort2(): ?string
    {
        return $this->port2;
    }

    public function setPort2(string $port2): self
    {
        $this->port2 = $port2;

        return $this;
    }

    public function getConnexionName(): ?string
    {
        return $this->email;
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

    /**
     * @return Collection<int, Mail>
     */
    public function getMails(): Collection
    {
        return $this->mails;
    }

    public function addMail(Mail $mail): self
    {
        if (!$this->mails->contains($mail)) {
            $this->mails->add($mail);
            $mail->setDsn($this);
        }

        return $this;
    }

    public function removeMail(Mail $mail): self
    {
        if ($this->mails->removeElement($mail)) {
            // set the owning side to null (unless already changed)
            if ($mail->getDsn() === $this) {
                $mail->setDsn(null);
            }
        }

        return $this;
    }
    


}
