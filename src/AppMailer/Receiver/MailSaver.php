<?php
    
    namespace App\AppMailer\Receiver;

use App\Entity\Mo;
use App\Entity\Mr;
use App\Entity\Dsn;
use App\Entity\Lead;
use App\Entity\Mail;
use App\Entity\Step;
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
            $mails = $this->allrec->receive($md->dsn,$md->compaignStartTime,$md->criteria);
          
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
                        $step = $this->getStep($mail->getTextHtml(),$lead);
                        if($step == null) continue;

                        $compaign = $lead->getCompaign();

                        if($step->getCompaign() != $compaign) continue;

        
                        $dsn = $lead->getDsn();
                        $sender = $mail->getFromAddress();
                        
                        
                        // $mr = $lead->getMr()->getValues();
                        $mr = $this->crud->getMrByStepAndLead($compaign->getId(),$step->getId(),$lead->getId());
                        
                        if($mr == null)
                        {
                            $Status = $this->crud->getStatus(STATUS::LEAD_COMPLETE);
                            $lead->setStatus($Status);
                            $lead->setNextStep(null);
                            $lead->addUniqMail($mail);

                            $mr = (new Mr)->setDsn($dsn)->setSender($sender)->setMrLead($lead)->setStep($step)->setCompaign($compaign);
                            $this->crud->saveMr($mr,true);

                            $mo = $this->crud->getMoByStepAndLead($compaign->getId(),$step->getId(),$lead->getId());
                            if($mo == null )
                            {
                                $mo = (new Mo)->setDsn($dsn)->setSender($sender)->setMoLead($lead)->setStep($step)->setCompaign($compaign);
                                $this->crud->saveMo($mo,true);

                            }
                        }
             

                        
                        
                    
                        $this->crud->saveLead($lead,true);
                        // $this->crud->saveCompaign($compaign,false);


                    }
                }

                
            }
            // if($lead!=null) $this->crud->em->flush();

        }

        private function getStep(?string $htmlString,Lead $lead)
        {
            // dd($htmlString);
            if($htmlString == null) return false;
            $dom = new \DOMDocument();
            $test = @$dom->loadHTML($htmlString);
            // dd($test);
            $html = $dom->saveHTML();
            // dd($html);

            $list = $dom->getElementsByTagName('samp');
            if($list)
            {
            
                for($i = 0; $i<$list->length; $i++)
                {
                    $node = $list->item($i);
                    if($node->hasAttributes())
                    {
                        $attrs = $node->attributes;
                        for($j = 0; $j < $attrs->length; $j++)
                        {
                            $attr = $attrs->item($j);
                            if($attr->nodeName == "style")
                            {
                                $text = $attr->nodeValue;
                                $texts = explode(":",$text);
                                
                                $id = trim($texts[1],";");
                                $id = intval($id);
                                return $this->crud->getStep($id);
                            }
                        }
                          
                    }
    
                }
                // return $lead->getStep();
                return null;

            }
            // $element = $dom->getElementById('stepId');
            // if($element)
            // {

            //     if($element->hasAttribute('name')) $stepId = $element->getAttribute('name');

            //     if(!$stepId) return $this->crud->getStep($stepId);
            // }
            // else return $lead->getStep();
            
        }


        // /**@param Dsn $dsn */
        // public function getMails(Dsn $dsn,mixed $criteria =1,$compaignStartTime)
        // {

        //     return $this->allrec->receive($dsn,$criteria,$compaignStartTime);

        // }

        // /**@param Lead $lead */
        // private function getAllmailIds($lead)
        // {
            
        //     /**@var Mail[] */
        //     $mails = $lead->getMail()->getValues();
        //     if($mails == null) return null;
        //     foreach($mails as $mail)
        //     {
        //         $mailIds[] = $mail->getMailId();
        //     }
        //     return $mailIds;
        // }
        
    }