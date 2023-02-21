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
        private $entityManager;

        /**
         * @var Sequencer
         */
        private $sequencer;

        /**
         * @var EmailSender
         */
        private $emailSender;

        public function __construct(ManagerRegistry $doctrine,EmailSender $emailSender,private CrudControllerHelpers $crud)
        {
            $this->entityManager = $doctrine->getManager();
            $this->emailSender = $emailSender;


      
        }
        public function prepare(Sequencer $sequencer)
        {   
            $this->sequencer = $sequencer;
            $this->leads = $this->sequencer->getLeads();
            // $this->flag = $this->sequencer->getFlag();
            $this->dsns = $this->sequencer->getDsns();
            $this->email = $this->sequencer->getEmail();
        }

 
        private function getNextLead():Lead
        {   
            $lead = $this->leads[$this->flag];
            $this->flag++;
            return $lead;
        }

        private function contains($tab,$item)
        {
            foreach ($tab as $key => $value) {
                if($item===$value) return $key;
            }
            return false;
        }
    
        private function getNextLeadStatus($tab,$lastStatus)
        {
            $count = count($tab);
            $key=$this->contains($tab,$lastStatus);
            
            $key++;
    
            if($key >= $count ) return 'lead.complete';
            return $tab[$key];
    
    
        }
        



        

        public function lunch()
        {
            /**
             * 
             * @var Dsn $Dsn
             */
            foreach($this->dsns as $Dsn)
            {   
                $lead = $this->getNextLead();

            
                if($Dsn->sendState == true) continue;

             
                if($lead->getSender() != null) $dsn = $Dsn->getDsnByEmail($lead->getSender());
                else $dsn = $Dsn->getDsn();

                
                $from = $Dsn->getEmail();

                $emailAddress =$lead->getEmailAddress();

                $this->emailSender->prepare($dsn,$from,$emailAddress,$this->email);

                if($this->emailSender->send())
                {
                    $Dsn->sendState = true;
                    $lead->setStatus($this->crud->getStatus('lead.status.sent'));
                    if($lead->getSender() == null) $lead->setSender($from);
                    
                    $this->crud->saveLead($lead);
                 
                }


                

            }
        }
    }