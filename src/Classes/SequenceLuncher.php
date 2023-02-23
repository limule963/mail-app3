<?php
namespace App\Classes;

use App\Entity\Dsn;
use App\Entity\Lead;


use App\Data\Sequence;
use Symfony\Component\Mime\Email;
use Doctrine\Persistence\ObjectManager;
use App\Controller\CrudControllerHelpers;
use App\Data\EmailData;

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

        public $leadStatusTable;

        public function __construct(/*ManagerRegistry $doctrine,*/EmailSender $emailSender,private CrudControllerHelpers $crud)
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
            $this->leadStatusTable = $sequence->leadStatusTable;

            foreach($this->dsns as $Dsn)
            {   
                $lead = $this->getNextLead();

            
                if($Dsn->sendState == true) continue;

             
                if($lead->getSender() != null) $dsn = $this->getDsnByEmail($lead->getSender());
                else $dsn = $Dsn->getDsn();

                
                $from = $Dsn->getEmail();

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
        



        private function getNextLead():Lead
        {   
            $lead = $this->leads[$this->flag];
            $this->flag++;
            return $lead;
        }

        private function getDsnByEmail($email)
        {
            foreach($this->dsns as $dsn)
            {
                if($dsn->getEmail() == $email) return $dsn->getDsn();
            }
        }

    }