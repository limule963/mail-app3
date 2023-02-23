<?php
namespace App\Classes;

use App\Entity\Dsn;
use App\Entity\Lead;


use App\Data\Sequence;
use Symfony\Component\Mime\Email;
use Doctrine\Persistence\ObjectManager;
use App\Controller\CrudControllerHelpers;
use App\Data\EmailData;
use App\Data\STATUS;

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
        
        private $compaignId;

        private $stepLeadStatus;

        public function __construct(EmailSender $emailSender,private CrudControllerHelpers $crud)
        {
            // $this->entityManager = $doctrine->getManager();
            $this->emailSender = $emailSender;
      
        }

        public function lunch(Sequence $sequence)
        {
            $this->sequence = $sequence;
            $this->leads = $sequence->leads;
            $this->dsns = $sequence->dsns;
            $this->email = $sequence->email;
            $this->compaignId = $sequence->compaignId;
            $this->stepLeadStatus = $sequence->stepLeadStatus;

            /**@var Dsn $Dsn */
            foreach($this->dsns as $Dsn)
            {   
                
                if($Dsn->sendState == true) continue;

                $from = $Dsn->getEmail();
                
                if($this->stepLeadStatus == STATUS::LEAD_STEP_1)
                {
                    $lead = $this->getNextLead('');
                    $dsn = $Dsn->getDsn();
                }
                else
                {
                    $lead = $this->getNextLead($from);
                    $dsn = $this->getDsnByEmail($lead->getSender());
                } 

                
                $emailAddress =$lead->getEmailAddress();

                $emailData = new EmailData($dsn,$from,$emailAddress,$this->email);

                $emailResponse = $this->emailSender->send($emailData);
                
                if($emailResponse->succes)
                {
                    $Dsn->sendState = true;
                    $status =$this->sequence->getNextLeadStatus($lead->getStatus()->getStatus());

                    $Status = $this->crud->getStatus($status);
                    $lead->setStatus($Status);
                    if($lead->getSender() == null) $lead->setSender($from);
                    
                    $this->crud->saveLead($lead);
                 
                }
                else return $emailResponse;

            }

        }
        



        // private function getNextLead():Lead
        // {   
        //     $lead = $this->leads[$this->flag];
        //     $this->flag++;
        //     return $lead;
        // }
        private function getNextLead($sender):Lead
        {   
            return $this->crud->getLeadBySender($this->compaignId,$this->stepLeadStatus,$sender);
        }

        private function getDsnByEmail($email)
        {
            foreach($this->dsns as $dsn)
            {
                if($dsn->getEmail() == $email) return $dsn->getDsn();
            }
        }

    }