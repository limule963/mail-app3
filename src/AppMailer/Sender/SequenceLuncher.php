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

        public function lunch(?Sequence $sequence)
        {
            if($sequence == null) return null;
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
                $senderName = $Dsn->getName();
                
                if($sequence->stepStatus == STATUS::STEP_1) $lead = $this->getNextLead();
                else $lead = $this->getNextLead($from);

                if($lead == null) continue;

                // if($this->isEmailAnswered($lead)) 
                // {
                //     $Status  = $this->crud->getStatus(STATUS::LEAD_COMPLETE);
                //     $lead->setStatus($Status);
                //     $this->crud->saveLead($lead,true);
                //     continue;
                // }


                $emailData = new EmailData($dsn,$from,$lead,$sequence->email,$senderName,$sequence->stepStatus);

                $emailResponse = $this->emailSender->send($emailData);
                
                if($emailResponse->succes)
                {
                    $Dsn->sendState = true;
                    
                    $this->prepareForNextCall($lead,$from,true);
                    
                }
                else $this->prepareForNextCall($lead,$from,false);

                $this->cr->setStat($emailResponse,$sequence->compaignState);

            }

            return $this->cr;
            

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
            if($lead->getMail()->getValues() != null) return true;
            return false;
        }
        // private function isEmailAnswered(Dsn $dsn,Lead $lead)
        // {
        //     $stamp = $dsn->getCreateAt()->getTimestamp();
        //     $date =getdate($stamp);
        //     $criteria = 'SINCE '.$date['year'].'-'.$date['mon'].'-'.$date['mday'];
        //     $criteria = $criteria.' '. 'FROM '.$lead->getEmailAddress();


        //     $mails = $this->allrec->receive($dsn,$criteria);
        //     foreach($mails as $key => $mail)
        //     {
        //         if($key == FOLDER::JUNK)
        //         {
        //             if($mails[FOLDER::JUNK] != null ) return true;
        //         }
        //         else if($key == FOLDER::INBOX)
        //         {
        //             if($mails[FOLDER::INBOX] != null) return true;
        //         }
        //     }
        //     return false;
        // }


        // private function getTemplatedEmail(Email $email,Lead $lead):TemplatedEmail
        // {
        //     $subject = $email->getSubject();
        //     $emailLink = $email->getEmailLink();

        //     $email = (new TemplatedEmail())
        //         // ->to(new Address('ryan@example.com'))
        //         ->subject($subject)

        //         // path of the Twig template to render
        //         ->htmlTemplate($emailLink)

        //         // pass variables (name => value) to the template
        //         ->context([
        //             'lead' => $lead
        //         ])
        //     ;
        //     $this->bodyRenderer->render($email);

        //     return $email;
        // }

        // private function getDsnByEmail($email)
        // {
        //     foreach($this->dsns as $dsn)
        //     {
        //         if($dsn->getEmail() == $email) return $dsn->getDsn();
        //     }
        // }

 

    }