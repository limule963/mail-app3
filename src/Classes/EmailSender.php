<?php
    namespace App\Classes;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

    class EmailSender
    {
        private string $dsn;
        private string $emailAddress;
        private string $from;

        /**
         * @var Email
         */

        private $email;

        public function __construct()
        {
            
        }
        public function prepare(string $dsn, string $from,string $emailAddress,Email $email)
        {
            $this->dsn = $dsn;
            $this->emailAddress = $emailAddress;
            $this->email = $email;
            $this->from = $from;

        }

        public function send()
        {
            $transport = Transport::fromDsn($this->dsn);
            $mailer = new Mailer($transport);

            $email = $this->email
                ->from($this->from)
                ->to($this->emailAddress)
            ;
            

            try 
            {

                $mailer ->send($email);
                
                return true;

            }
            catch (\Throwable $th) 
            {
                $message =  $th->getMessage();
                return false;
                
            }
        }
    }
