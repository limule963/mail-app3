<?php
namespace App\AppMailer\Sender;
    

use App\Entity\Dsn;
use App\Entity\Lead;
use App\Entity\Email;
use App\AppMailer\Data\FOLDER;
use App\AppMailer\Data\EmailData;
use App\Event\MailPreRenderEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Mailer;
use App\AppMailer\Data\EmailResponse;
use App\Listener\MailPreRenderListener;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email as Em;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

    class EmailSender
    {

        public function __construct(private BodyRendererInterface $bodyRenderer)
        {
            
        }

        public function send(EmailData $ed)
        {
            $transport = Transport::fromDsn($ed->dsn);
            $mailer = new Mailer($transport);

            $email = $this->getTemplatedEmail($ed->email,$ed->lead,$ed->from,$ed->senderName,$ed->tracker,$ed->stepId);

            // dd($email);

            try 
            {
                $mailer ->send($email);
                return new EmailResponse(true,'Email sent',$ed->from,$ed->lead->getEmailAddress(),$ed->stepStatus);

            }
            catch (\Throwable $th) 
            {
                return new EmailResponse(false,'Email not sent',$ed->from,$ed->lead->getEmailAddress(),$ed->stepStatus,$th->getCode(),$th->getMessage());   
            }
        }




        private function getTemplatedEmail(Email $email,Lead $lead,string $from,string $senderName,bool $tracker,int $stepId):TemplatedEmail
        {
            $subject = $email->getSubject();
            $emailLink = $email->getEmailLink();

            $emailAddress = $lead->getEmailAddress();
            // $tracker =$addTracker;
            $variables = [
                'lead' => $lead,
                'tracker'=> $tracker,
                'stepId'=>$stepId
            ];

            $email = (new TemplatedEmail())
                // ->to(new Address('ryan@example.com'))
                ->subject($subject)
                ->from(new Address($from,$senderName))
                ->to(new Address($emailAddress,$lead->getName()))
                // path of the Twig template to render
                ->htmlTemplate($emailLink)
                // ->addPart((new DataPart(fopen('https://aykode.com/images/8601571909526073.png', 'r'), 'image1', 'image/png'))->asInline())

                // pass variables (name => value) to the template

                ->context($variables)
            ;
            
            $this->bodyRenderer->render($email);

            // $event = new MailPreRenderEvent($variables,$email->getHtmlBody());
            // $listener = new MailPreRenderListener;
            // $dispatcher = new EventDispatcher();
            // $dispatcher->addListener(MailPreRenderEvent::NANE,[$listener,'onMailPreRender']);

            // $dispatcher->dispatch($event,MailPreRenderEvent::NANE);


            return $email;
        }


    }
