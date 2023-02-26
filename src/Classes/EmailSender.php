<?php
    namespace App\Classes;

use App\Entity\Lead;
use App\Data\EmailData;
use App\Data\EmailResponse;
use App\Entity\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\BodyRendererInterface;

    class EmailSender
    {

        public function __construct(private BodyRendererInterface $bodyRenderer)
        {
            
        }

        public function send(EmailData $ed)
        {
            $transport = Transport::fromDsn($ed->dsn);
            $mailer = new Mailer($transport);

            $email = $this->getTemplatedEmail($ed->email,$ed->lead,$ed->from,$ed->senderName);
            // dd($email);



            try 
            {
                $mailer ->send($email);
                return new EmailResponse(true,'Email sent',$ed->from,$ed->lead->getEmailAddress(),$ed->stepStatus);

            }
            catch (\Throwable $th) 
            {
                return new EmailResponse(false,'Email not sent',$ed->from,$ed->lead->getEmailAddress(),$th->getCode(),$th->getMessage());   
            }
        }




        private function getTemplatedEmail(Email $email,Lead $lead,string $from,string $senderName):TemplatedEmail
        {
            $subject = $email->getSubject();
            $emailLink = $email->getEmailLink();

            $emailAddress = $lead->getEmailAddress();
            

            $email = (new TemplatedEmail())
                // ->to(new Address('ryan@example.com'))
                ->subject($subject)
                ->from(new Address($from,$senderName))
                ->to(new Address($emailAddress,$lead->getName()))


                // path of the Twig template to render
                ->htmlTemplate($emailLink)

                // pass variables (name => value) to the template
                ->context([
                    'lead' => $lead
                ])
            ;
            $this->bodyRenderer->render($email);

            return $email;
        }
    }
