<?php
namespace App\Classes;

use App\Entity\Dsn;
use App\Entity\Lead;
use App\Entity\Email as Em;


use App\Data\Sequence;
use Symfony\Component\Mime\Email;
use Doctrine\Persistence\ObjectManager;
use App\Controller\CrudControllerHelpers;
use App\Data\CompaignResponse;
use App\Data\EmailData;
use App\Data\STATUS;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\BodyRendererInterface;

    class SequenceLuncher
    {
        /**
         * @var Dsn[]
         */
        private $dsns;



        /**
         * @var Sequence
         */
        private $sequence;

        /**
         * @var EmailSender
         */
        private $emailSender;
        
        // private $compaignId;


        public function __construct(EmailSender $emailSender,private CrudControllerHelpers $crud, private CompaignResponse $cr,private BodyRendererInterface $bodyRenderer)
        {
            // $this->entityManager = $doctrine->getManager();
            $this->emailSender = $emailSender;
      
        }

        public function lunch(Sequence $sequence)
        {
            $this->sequence = $sequence;
            $this->dsns = $sequence->dsns;
            // $this->email = $sequence->email;
            // $this->compaignId = $sequence->compaignId;
            // $this->stepStatus = $sequence->stepStatus;

            /**@var Dsn $Dsn */
            foreach($this->dsns as $Dsn)
            {   
                
                if($Dsn->sendState == true) continue;
                
                $from = $Dsn->getEmail();
                $dsn = $Dsn->getDsn();
                
                if($sequence->stepStatus == STATUS::STEP_1) $lead = $this->getNextLead();
                else $lead = $this->getNextLead($from);

                if($lead == null) continue;

                
                $email = $this->getTemplatedEmail($sequence->email,$lead);

                $emailAddress =$lead->getEmailAddress();

                $emailData = new EmailData($dsn,$from,$emailAddress,$email,$sequence->stepStatus);

                $emailResponse = $this->emailSender->send($emailData);
                
                if($emailResponse->succes)
                {
                    $Dsn->sendState = true;
                    $status =$this->sequence->getNextLeadStatus($lead->getStatus()->getStatus());

                    $Status = $this->crud->getStatus($status);
                    $lead->setStatus($Status);
                    if($lead->getSender() == '') $lead->setSender($from);
                    
                    $this->crud->saveLead($lead);
                 
                }
                $this->cr->setStat($emailResponse);

            }

            return $this->cr;
            

        }
        


        private function getNextLead($sender = ''):Lead|null
        {   
            return $this->crud->getLeadBySender($this->sequence->compaignId,$this->sequence->stepStatus,$sender);
        }


        private function getTemplatedEmail(Em $email,Lead $lead):Email
        {
            $subject = $email->getSubject();
            $emailLink = $email->getEmailLink();

            $email = (new TemplatedEmail())
                // ->to(new Address('ryan@example.com'))
                ->subject($subject)

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

        // private function getDsnByEmail($email)
        // {
        //     foreach($this->dsns as $dsn)
        //     {
        //         if($dsn->getEmail() == $email) return $dsn->getDsn();
        //     }
        // }

 

    }