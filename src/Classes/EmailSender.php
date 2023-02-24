<?php
    namespace App\Classes;

use App\Data\EmailData;
use App\Data\EmailResponse;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

    class EmailSender
    {

        public function __construct()
        {
            
        }

        public function send(EmailData $ed)
        {
            $transport = Transport::fromDsn($ed->dsn);
            $mailer = new Mailer($transport);

            $email = $ed->email
                ->from($ed->from)
                ->to($ed->emailAddress)
            ;

            try 
            {
                $mailer ->send($email);
                return new EmailResponse(true,'Email sent',$ed->from,$ed->emailAddress);

            }
            catch (\Throwable $th) 
            {
                return new EmailResponse(false,'Email not sent','',$ed->emailAddress,$th->getCode(),$th->getMessage());   
            }
        }
    }
