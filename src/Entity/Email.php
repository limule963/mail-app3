<?php

namespace App\Entity;

use App\Repository\EmailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

#[ORM\Entity(repositoryClass: EmailRepository::class)]
class Email
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,nullable:true)]
    private ?string $emailLink = null;

    #[ORM\Column(length: 255,nullable:true)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textMessage = null;

    #[ORM\Column]
    public ?string $uid = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $trackingLink = null;


    public function __construct()
    {
        if($this->uid == null) $this->uid = uniqid();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmailLink(): ?string
    {
        return $this->emailLink;
    }

    public function setEmailLink(string $emailLink): self
    {
        $this->emailLink = $emailLink;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getEmail():TemplatedEmail
    {  

        $email = (new TemplatedEmail())
            ->subject($this->getSubject())
            // path of the Twig template to render
            ->htmlTemplate($this->getEmailLink())
        ;
        return $email;
    }

    public function getTextMessage(): ?string
    {
        return $this->textMessage;
    }

    public function setTextMessage(?string $textMessage): self
    {
        $this->textMessage = $textMessage;

        return $this;
    }

    public function getTrackingLink(): ?string
    {
        return $this->trackingLink;
    }

    public function setTrackingLink(?string $trackingLink): self
    {
        $this->trackingLink = $trackingLink;

        return $this;
    }
}
