<?php
    namespace App\AppMailer\Sender;





use App\Entity\Dsn;
use App\Entity\Lead;
use App\Entity\Email;
use App\AppMailer\Data\STATUS;
use App\AppMailer\Data\FOLDER;
use App\AppMailer\Data\Sequence;
use App\AppMailer\Data\EmailData;
use App\AppMailer\Data\CompaignResponse;
use App\Controller\CrudControllerHelpers;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\AppMailer\Receiver\AllFolderReceiver;
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


        public function __construct(EmailSender $emailSender,private CrudControllerHelpers $crud, private CompaignResponse $cr,private BodyRendererInterface $bodyRenderer,private AllFolderReceiver $allrec)
        {
            // $this->entityManager = $doctrine->getManager();
            $this->emailSender = $emailSender;
      
        }

        public function lunch(?Sequence $seq)
        {
            if($seq == null) return null;
            $this->sequence = $seq;
            $this->dsns = $seq->dsns;
 
            $tms = 0;
            // $tmo = 0;
            $tmr = 0;

            /**@var Dsn $Dsn */
            foreach($this->dsns as $Dsn)
            {   
                
                if($Dsn->sendState == true) continue;
                
                $from = $Dsn->getEmail();
                $dsn = $Dsn->getDsn();
                $senderName = $Dsn->getName();
                
                if($seq->stepStatus == STATUS::STEP_1) $lead = $this->getNextLead();
                else $lead = $this->getNextLead($from);

                if($lead == null) continue;

                if($this->isEmailAnswered($lead)) 
                {
                    $tmr++;
                    $Status  = $this->crud->getStatus(STATUS::LEAD_COMPLETE);
                    $lead->setStatus($Status);
                    $this->crud->saveLead($lead,true);
                    continue;
                }


                $emailData = new EmailData($dsn,$from,$lead,$seq->email,$senderName,$seq->stepStatus,$seq->tracker);

                $emailResponse = $this->emailSender->send($emailData);
                
                if($emailResponse->succes)
                {
                    $tms++;
                    $Dsn->sendState = true;
                    
                    $this->prepareForNextCall($lead,$from,true);
                    
                }
                else $this->prepareForNextCall($lead,$from,false);

                $this->cr->setResponse($emailResponse);

            }
            $stepTms = $seq->step->getTms();
            $stepTmr = $seq->step->getTmr();
            $seq->step->setTms($stepTms+$tms);
            $seq->step->setTmr($stepTmr+$tmr);
            $this->crud->saveStep($seq->step);
        
            return $this->cr->setCompagneState($seq->compaignState)->setTms($tms)->setTmr($tmr);
            

        }


        private function prepareForNextCall(Lead $lead,$from,bool $isMailSend)
        {
            
            if($isMailSend) 
            {
                if($lead->getSender() == '') 
                {
                    
                    $lead->setSender($from);
                }
                $status =$this->sequence->getNextLeadStatus($lead->getStatus()->getStatus());
                $Status = $this->crud->getStatus($status);
                $lead->setStatus($Status);
            }
            else 
            {
                $status = STATUS::LEAD_FAIL;
                $Status = $this->crud->getStatus($status);
                $lead->setStatus($Status);
                $lead->setSender($from.'|Not Send');
            }

            $this->crud->saveLead($lead);

        }
        


        private function getNextLead($sender = ''):Lead|null
        {   
            return $this->crud->getLeadBySender($this->sequence->compaignId,$this->sequence->stepStatus,$sender);
        }




        
        private function isEmailAnswered(Lead $lead)
        {
            if(!empty($lead->getMail()->getValues())) return true;
            return false;
        }

    }