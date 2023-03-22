<?php
    
    namespace App\AppMailer\Receiver;

use App\Entity\Mo;
use App\Entity\Mr;
use App\Entity\Dsn;
use App\Entity\Lead;
use App\Entity\Mail;
use App\AppMailer\Data\FOLDER;
use App\AppMailer\Data\STATUS;

use App\AppMailer\Data\MailData;
use App\AppMailer\Data\EmailResponse;
use function PHPSTORM_META\elementType;
use App\Controller\CrudControllerHelpers;

    class MailSaver
    {
        private $mails;
        public function __construct(private AllDsnReceiver $allDsnRec,private CrudControllerHelpers $crud,private AllFolderReceiver $allrec)
        {
            
        }

        public function saveMails(MailData $md)
        {
            $lead = null;
            $mails = $this->allrec->receive($md->dsn,$md->criteria,$md->compaignStartTime);
            if($mails == null) return null;
            if($mails instanceof EmailResponse) return null;
            /**@var Mail[] $mails */

         

            foreach($mails as $mailarray)
            {
                /**@var Mail $mail */
                foreach($mailarray as $mail)
                {

                    $lead =$this->crud->getLeadByEmailAddress($md->compaignId,$mail->getFromAddress());
                    if($lead == null) continue;
                    else
                    {
                        $Status = $this->crud->getStatus(STATUS::LEAD_COMPLETE);
                        $lead->setStatus($Status);
                        $lead->setNextStep(null);
                        $lead->addUniqMail($mail);

                        $step = $lead->getStep();
                        $compaign = $lead->getCompaign();
                        $sender = $mail->getFromAddress();


                      
                        $mr = (new Mr)->setSender($sender)->setMrLead($lead)->setStep($step)->setCompaign($compaign);
                        $this->crud->saveMr($mr,false);

                      
                        $mo = (new Mo)->setSender($sender)->setMoLead($lead)->setStep($step)->setCompaign($compaign);
                        $this->crud->saveMo($mo,false);
                        
                    
                        $this->crud->saveLead($lead,false);
                        $this->crud->saveCompaign($compaign,false);


                    }
                }

                
            }
            if($lead!=null) $this->crud->em->flush();

        }


        /**@param Dsn $dsn */
        public function getMails(Dsn $dsn,mixed $criteria =1,$compaignStartTime)
        {

            return $this->allrec->receive($dsn,$criteria,$compaignStartTime);

        }

        /**@param Lead $lead */
        private function getAllmailIds($lead)
        {
            
            /**@var Mail[] */
            $mails = $lead->getMail()->getValues();
            if($mails == null) return null;
            foreach($mails as $mail)
            {
                $mailIds[] = $mail->getMailId();
            }
            return $mailIds;
        }
        
    }