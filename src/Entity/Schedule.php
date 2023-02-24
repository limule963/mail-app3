<?php

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScheduleRepository::class)]
class Schedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $fromm = null;

    #[ORM\Column]
    private ?int $too = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startTime = null;

    // #[ORM\OneToOne(inversedBy:'schedule')]
    // #[ORM\JoinColumn(nullable: false)]
    // private ?Compaign $compaign= null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromm(): ?int
    {
        return $this->fromm;
    }

    public function setFromm(int $fromm): self
    {
        $this->fromm = $fromm;

        return $this;
    }

    public function getToo(): ?int
    {
        return $this->too;
    }

    public function setToo(int $too): self
    {
        $this->too = $too;

        return $this;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeImmutable $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    // public function getCompaign(): ?Compaign
    // {
    //     return $this->compaign;
    // }

    // public function setCompaign(?Compaign $compaign): self
    // {
    //     $this->compaign = $compaign;

    //     return $this;
    // }
}
