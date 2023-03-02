<?php
    
    namespace App\AppMailer\Receiver;

use App\Entity\Dsn;
use App\Entity\Lead;
use App\Entity\Mail;
use App\AppMailer\Data\FOLDER;
use App\AppMailer\Data\MailData;

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
            $mails = $this->allrec->receive($md->dsn,$md->criteria);
            /**@var Mail[] $mails */
            foreach($mails as $mailarray)
            {
                if($mailarray == null) continue;
                else
                {
                    foreach($mailarray as $mail)
                    {

                        $lead =$this->crud->getLeadByEmailAddress($md->compaignId,$mail->getFromAddress());
                        if($lead == null) continue;
                        else
                        {
                            $mailIds = $this->getAllmailIds($lead);
                            if($mailIds==null) $lead->addMail($mail);
                            else
                            {
                                if(!in_array($mail->getMailId(),$mailIds,true)) $lead->addMail($mail);
                            }
                            $this->crud->saveLead($lead,false);
    
                        }
                    }

                }
            }
            if($lead!=null) $this->crud->saveLead($lead);

            /**@var ?Mail[]  */
            // $inboxFolder = $mails[FOLDER::INBOX];

            /**@var ?Mail[]  */
            // $junkFolder = $mails[FOLDER::JUNK];
            
            // /**@var ?Lead */
            // $lead = null;
            // if($inboxFolder!=null)
            // {
            //     foreach($inboxFolder as $mail)
            //     {
            //         $lead =$this->crud->getLeadByEmailAddress($md->compaignId,$mail->getFromAddress());

            //         if($lead!=null) 
            //         {
            //             $mailIds = $this->getAllmailIds($lead);
            //             if($mailIds!=null)
            //             $id = $mail->getMailId();
                     
            //             if(!in_array($id,$mailIds,true)) $lead->addMail($mail);
            //             $this->crud->saveLead($lead,true);
            //         }

            //     }
            // }


            // if($junkFolder!=null)
            // {
            //     foreach($inboxFolder as $mail)
            //     {
            //         $mailIds = $this->getAllmailIds($lead);
            //         $id = $mail->getMailId();
                 
            //         if(!in_array($id,$mailIds,true)) $lead->addMail($mail);
            //     }
            //     $this->crud->saveLead($lead,true);

            // }

            
            

        }


        /**@param Dsn $dsn */
        public function getMails(Dsn $dsn,mixed $criteria =1)
        {

            return $this->allrec->receive($dsn,$criteria);

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