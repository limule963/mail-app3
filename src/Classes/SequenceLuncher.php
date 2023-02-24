<?php
namespace App\Classes;

use App\Entity\Dsn;
use App\Entity\Lead;


use App\Data\Sequence;
use Symfony\Component\Mime\Email;
use Doctrine\Persistence\ObjectManager;
use App\Controller\CrudControllerHelpers;
use App\Data\CompaignResponse;
use App\Data\EmailData;
use App\Data\STATUS;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

    class SequenceLuncher
    {
        /**
         * @var Dsn[]
         */
        private $dsns;

        /**
         * @var Email
         */
        private $email;

        /**
         * @var array
         */
        private $leads;

        private $flag = 0;

        /**
         * @var ObjectManager
         */
        // private $entityManager;

        /**
         * @var Sequence
         */
        private $sequence;

        /**
         * @var EmailSender
         */
        private $emailSender;
        
        // private $compaignId;

        private $stepStatus;

        public function __construct(EmailSender $emailSender,private CrudControllerHelpers $crud, private CompaignResponse $cr)
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
                
                if($this->stepStatus == STATUS::STEP_1) $lead = $this->getNextLead('');
                else $lead = $this->getNextLead($from);

                
                $email = $this->emailFinalizer($sequence->email,$lead);

                $emailAddress =$lead->getEmailAddress();

                $emailData = new EmailData($dsn,$from,$emailAddress,$email);

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
        
        private function emailFinalizer(TemplatedEmail $email,Lead $lead):Email
        {
            return $email->context(
                ['lead.name'=> $lead->getName()]
            );
        }



        // private function getNextLead():Lead
        // {   
        //     $lead = $this->leads[$this->flag];
        //     $this->flag++;
        //     return $lead;
        // }
        private function getNextLead($sender):Lead
        {   
            return $this->crud->getLeadBySender($this->sequence->compaignId,$this->sequence->stepStatus,$sender);
        }

        // private function getDsnByEmail($email)
        // {
        //     foreach($this->dsns as $dsn)
        //     {
        //         if($dsn->getEmail() == $email) return $dsn->getDsn();
        //     }
        // }

    }