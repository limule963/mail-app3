<?php

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScheduleRepository::class)]
class Schedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(length: 255)]
    private ?string $fromm = null;

    #[ORM\Column(length: 255)]
    private ?string $too = null;

    #[ORM\Column(length: 255)]
    private ?string $timezone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getFromm(): ?string
    {
        return $this->fromm;
    }

    public function setFromm(string $fromm): self
    {
        $this->fromm = $fromm;

        return $this;
    }

    public function getToo(): ?string
    {
        return $this->too;
    }

    public function setToo(string $too): self
    {
        $this->too = $too;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }
}
