<?php
namespace App\Classes;

use App\Entity\Lead;


use Symfony\Component\Mime\Email;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
 

    
   

    class SequenceLuncher
    {
        /**
         * @var array
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
        // /**
        //  * @var Flag
        //  */
        // private $flag;

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

        public function __construct(ManagerRegistry $doctrine,EmailSender $emailSender)
        {
            $this->entityManager = $doctrine->getManager();
            $this->emailSender = $emailSender;


      
        }
        public function prepare(Sequencer $sequencer)
        {   
            $this->sequencer = $sequencer;
            $this->leads = $this->sequencer->getLeads();
            // $this->flag = $this->sequencer->getFlag();
            $this->dsns = $this->sequencer->getDsn();
            $this->email = $this->sequencer->getEmail();
        }

        /**
         * comment
         */
        private function getNextLead()
        {   
            $lead = $this->leads[$this->flag];
            $this->flag++;
            return $lead;
        }



        

        public function lunch()
        {
            foreach($this->dsns as $Dsn)
            {   
                $lead = $this->getNextLead();

                if($Dsn->sendState == true) continue;


                if($lead->getSender() != null) $dsn = $Dsn->getDsnByEmail($lead->getSender());
                else $dsn = $Dsn->getDsn();

                
                $from = $Dsn->getEmail();

                $emailAddress =$lead->getEmailAdress();

                $this->emailSender->prepare($dsn,$from,$emailAddress,$this->email);

                if($this->emailSender->send())
                {
                    $Dsn->sendState = true;
                    $lead->setStatus('lead.status.sent');
                    if($lead->getSender() == null) $lead->setSender($from);
                    


                    $this->entityManager->persist($lead);
                    $this->entityManager->flush();
                }


                

            }
        }
    }