<?php
    namespace App\AppMailer\Sender;





use App\Entity\Mo;
use App\Entity\Mr;
use App\Entity\Ms;
use App\Entity\Dsn;
use App\Entity\Lead;
use App\Entity\Step;
use App\Entity\Email;
use App\AppMailer\Data\FOLDER;
use App\AppMailer\Data\STATUS;
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
 

            /**@var Dsn $Dsn */
            foreach($this->dsns as $Dsn)
            {   
                
                if($Dsn->sendState == true) continue;

                $lead = $this->getNextLead();

                if($lead == null) continue;
                
                if($lead->getDsn() != null) $Dsn = $lead->getDsn();
                
                $from = $Dsn->getEmail();
                $dsn = $Dsn->getDsn();
                $senderName = $Dsn->getName();
            
 



                $emailData = new EmailData($dsn,$from,$lead,$seq->email,$senderName,$seq->stepStatus,$seq->tracker,$seq->step->getId());

                $emailResponse = $this->emailSender->send($emailData);
                
                if($emailResponse->succes)
                {
                  
                    $ms = (new Ms)->setDsn($Dsn)->setSender($from)->setMsLead($lead)->setStep($seq->step)->setCompaign($seq->step->getCompaign());
                    $this->crud->saveMs($ms,false);

                    $Dsn->sendState = true;
                    
                    $this->prepareForNextCall($Dsn,$seq->step,$lead,$from,true);
                    
                }
                else $this->prepareForNextCall($Dsn,$seq->step,$lead,$from,false);

                $this->cr->setResponse($emailResponse);

            }


            $this->crud->saveStep($seq->step,false);
            $this->crud->em->flush();
        
            return $this->cr->setCompagneState($seq->compaignState);
            

        }


        private function prepareForNextCall(Dsn $dsn,Step $step,Lead $lead,$from,bool $isMailSend)
        {
            
            if($isMailSend) 
            {
                
                if($lead->getDsn() == null) 
                {
                    
                    $lead->setSender($from);
                    $lead->setStatus($this->crud->getStatus(STATUS::LEAD_ONPROGRESS));
                    $lead->setDsn($dsn);
                }
                

                
                
                $compaign = $step->getCompaign();
                $lead->setStep($step);

                // $nextStep =$this->sequence->getNextStep();
                $nextStep = $this->crud->getNextStep($compaign->getId(),$step->getStepOrder()+1) ;

                if($nextStep == null) 
                {
                    $lead->setStatus($this->crud->getStatus(STATUS::LEAD_COMPLETE));
                }
                
                $lead->setNextStep($nextStep);
                $this->crud->saveLead($lead,false);


                // $Status = $this->crud->getStatus($status);
                // $lead->setStatus($Status);
            }
            else 
            {
                $status = STATUS::LEAD_FAIL;
                $Status = $this->crud->getStatus($status);
                $lead->setStep($lead->getNextStep());
                $lead->setStatus($Status);
                $lead->setSender('Send Failed');
                $this->crud->saveLead($lead,false);
            }

            $this->crud->saveLead($lead);

        }
        


        private function getNextLead():Lead|null
        {   
            return $this->crud->getLeadByNextStep($this->sequence->compaignId,$this->sequence->step->getId());
        }




        
        private function isEmailAnswered(Lead $lead)
        {
            if(!empty($lead->getMail()->getValues())) return true;
            return false;
        }

    }