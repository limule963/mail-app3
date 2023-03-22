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
 
            $tms = 0;
            // $tmo = 0;
            $tmr = 0;
            $tmo = 0;

            $ms = null;
            $mo = null;
            $mr =null;

            /**@var Dsn $Dsn */
            foreach($this->dsns as $Dsn)
            {   
                
                if($Dsn->sendState == true) continue;
                
                $from = $Dsn->getEmail();
                $dsn = $Dsn->getDsn();
                $senderName = $Dsn->getName();
                
                // if($seq->step->getStepOrder() == 0 ) $lead = $this->getNextLead();

                
                $lead = $this->getNextLead();
                // if($lead->getSender() != '') $lead = $this->getNextLead($from);
                

                if($lead == null) continue;

                // if($this->isEmailAnswered($lead)) 
                // {
                //     $tmr++;
                //     $mr = (new Mr)->setSender($from)->setMrLead($lead)->setStep($seq->step)->setCompaign($seq->step->getCompaign());
                //     $this->crud->saveMr($mr,false);

                //     $tmo++;
                //     $mo = (new Mo)->setSender($from)->setMoLead($lead)->setStep($seq->step)->setCompaign($seq->step->getCompaign());
                //     $this->crud->saveMo($mo,false);

                //     $Status  = $this->crud->getStatus(STATUS::LEAD_COMPLETE);
                //     $lead->setStatus($Status);
                //     $this->crud->saveLead($lead,true);
                //     continue;
                // }


                $emailData = new EmailData($dsn,$from,$lead,$seq->email,$senderName,$seq->stepStatus,$seq->tracker,$seq->step->getId());

                $emailResponse = $this->emailSender->send($emailData);
                
                if($emailResponse->succes)
                {
                    $tms++;
                    $ms = (new Ms)->setSender($from)->setMsLead($lead)->setStep($seq->step)->setCompaign($seq->step->getCompaign());
                    $this->crud->saveMs($ms,false);

                    $Dsn->sendState = true;
                    
                    $this->prepareForNextCall($seq->step,$lead,$from,true);
                    
                }
                else $this->prepareForNextCall($seq->step,$lead,$from,false);

                $this->cr->setResponse($emailResponse);

            }

            $stepTms = $seq->step->getTms();
            $stepTmr = $seq->step->getTmr();
            $stepTmo = $seq->step->getTmo();

            $seq->step->setTms($stepTms+$tms);
            $seq->step->setTmr($stepTmr+$tmr);
            $seq->step->setTmo($stepTmo+$tmo);

            $this->crud->saveStep($seq->step,false);
            $this->crud->em->flush();
        
            return $this->cr->setCompagneState($seq->compaignState)->setTms($tms)->setTmr($tmr)->setTmo($tmo);
            

        }


        private function prepareForNextCall(Step $step,Lead $lead,$from,bool $isMailSend)
        {
            
            if($isMailSend) 
            {
                if($lead->getSender() == '') 
                {
                    
                    $lead->setSender($from);
                    $lead->setStatus($this->crud->getStatus(STATUS::LEAD_ONPROGRESS));
                }
                
                
                $compaign = $step->getCompaign();
                $lead->setStep($step);

                // $nextStep =$this->sequence->getNextStep();
                $nextStep = $this->crud->getNextStep($compaign->getId(),$step->getStepOrder()+1) ;

                if($nextStep == null) $lead->setStatus($this->crud->getStatus(STATUS::LEAD_COMPLETE));

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
            }

            $this->crud->saveLead($lead);

        }
        


        private function getNextLead($sender = ''):Lead|null
        {   
            return $this->crud->getLeadBySender($this->sequence->compaignId,$this->sequence->step->getId(),$sender);
        }




        
        private function isEmailAnswered(Lead $lead)
        {
            if(!empty($lead->getMail()->getValues())) return true;
            return false;
        }

    }