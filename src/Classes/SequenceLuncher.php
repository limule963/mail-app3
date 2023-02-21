<?php
namespace App\Classes;

use App\Controller\CrudControllerHelpers;
use App\Entity\Dsn;


use App\Entity\Lead;
use Symfony\Component\Mime\Email;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
 

    
   

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
         * @var Sequencer
         */
        private $sequencer;

        /**
         * @var EmailSender
         */
        private $emailSender;

        public function __construct(/*ManagerRegistry $doctrine,*/EmailSender $emailSender,private CrudControllerHelpers $crud)
        {
            // $this->entityManager = $doctrine->getManager();
            $this->emailSender = $emailSender;


      
        }
        public function prepare(Sequencer $sequencer):self
        {   
            $this->sequencer = $sequencer;
            $this->leads = $this->sequencer->getLeads();
            $this->dsns = $this->sequencer->getDsns();
            $this->email = $this->sequencer->getEmail();

            return $this;
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



        

        public function lunch()
        {

            foreach($this->dsns as $Dsn)
            {   
                $lead = $this->getNextLead();

            
                if($Dsn->sendState == true) continue;

             
                if($lead->getSender() != null) $dsn = $this->getDsnByEmail($lead->getSender());
                else $dsn = $Dsn->getDsn();

                
                $from = $Dsn->getEmail();

                $emailAddress =$lead->getEmailAddress();

                $this->emailSender->prepare($dsn,$from,$emailAddress,$this->email);
                
                if($this->emailSender->send())
                {
                    $Dsn->sendState = true;
                    
                    $status =$this->sequencer->getNextLeadStatus($lead->getStatus()->getStatus());

                    $Status = $this->crud->getStatus($status);
                    $lead->setStatus($Status);
                    if($lead->getSender() == null) $lead->setSender($from);
                    
                    $this->crud->saveLead($lead);
                 
                }

            }
        }



    }