<?php
namespace App\AppMailer\Data;


use App\Entity\Dsn;
use App\Entity\Lead;
use App\Entity\Email;
    


    class EmailData
    {
        // public string $dsn;
        // public string $from;
        // public string $emailAddress;

        // public Email $email;

        public function __construct(public string $dsn,public string $from,  public Lead $lead, public Email $email,public string $senderName, public string $stepStatus)
        {
            
        }

    }