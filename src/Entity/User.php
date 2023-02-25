<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Compaign::class,cascade:["persist"])]
    private Collection $compaigns;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Dsn::class,cascade:['persist'])]
    private Collection $dsns;

    public function __construct()
    {
        $this->compaigns = new ArrayCollection();
        $this->dsns = new ArrayCollection();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $compaign->setUser($this);
        }

        return $this;
    }
    public function addCompaigns(array $compaigns):self
    {
        foreach($compaigns as $compaign)
        {
            $this->addCompaign($compaign);
        }
        return $this;
    }

    public function removeCompaign(Compaign $compaign): self
    {
        if ($this->compaigns->removeElement($compaign)) {
            // set the owning side to null (unless already changed)
            if ($compaign->getUser() === $this) {
                $compaign->setUser(null);
            }
        }

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
            $dsn->setUser($this);
        }

        return $this;
    }

    public function removeDsn(Dsn $dsn): self
    {
        if ($this->dsns->removeElement($dsn)) {
            // set the owning side to null (unless already changed)
            if ($dsn->getUser() === $this) {
                $dsn->setUser(null);
            }
        }

        return $this;
    }


}
