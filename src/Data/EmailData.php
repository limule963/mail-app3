<?php
    
    namespace App\Data;

use Symfony\Component\Mime\Email;

    class EmailData
    {
        // public string $dsn;
        // public string $from;
        // public string $emailAddress;

        // public Email $email;

        public function __construct(public string $dsn,public string $from,  public string $emailAddress, public Email $email, public string $stepStatus)
        {
            
        }

    }