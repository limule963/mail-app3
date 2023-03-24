<?php

namespace App\AppMailer\Receiver;

use PhpImap\Mailbox;
use App\Entity\Mail as Ma;
use App\AppMailer\Data\Mail;
use App\AppMailer\Data\Connexion;
use SecIT\ImapBundle\Service\Imap;
use App\AppMailer\Data\EmailResponse;


    class Receiver
    {

        /**@var Mail[] */
        public $mails;
        
        public function __construct()
        {

        }

        public function getMail(Connexion $connexion)
        {

            $dsn = $connexion->dsn;
            $folder = $connexion->folder;
            $criteria = $connexion->criteria;
            $imap = new Imap($dsn->getConnexion());

            $test = $imap->testConnection($dsn->getConnexionName());
            
            if(!$test) return new EmailResponse(false,'Connexion fail',$dsn->getEmail(),'');
            
            /**@var Mailbox */
            $con = $imap->get($dsn->getConnexionName());
            

            // $stamp = $dsn->getCreateAt()->getTimestamp();
            // $date =getdate($stamp);
            // $criteria = 'since '.$date['year'].'-'.$date['mon'].'-'.$date['mday'];
            
            try
            {
                $mailIds = $con->switchMailbox($folder)->sortMails(searchCriteria:$criteria);
                foreach($mailIds as $id)
                {
                    $mail2 = new Mail;
                    $mail = $con->getMail($id);
                    // dd($mail);
                    // if($mail == null) continue;
                    $mail2->subject = $mail->subject;
                    $mail2->from = $mail->fromAddress;
                    // $mail2->to = $mail->toString;
                    $mail2->isRecent = $mail->isRecent;
                    $mail2->isFlagged = $mail->isFlagged;
                    $mail2->isAnswered = $mail->isAnswered;
                    $mail2->isDraft = $mail->isDraft;
                    $mail2->isSeen = $mail->isSeen;
                    $mail2->textHtml = $mail->textHtml;
                    $mail2->textPlain = $mail->textPlain;
                    $mail2->isDeleted = $mail->isDeleted;
                    $mail2->date = $mail->date;
                    $mail2->mid = $mail->id;
                    $mail2->textHtml = $mail->textHtml;
                    $mail2->textPlain = $mail->textPlain;
                    // $mail2->udate = $mail->udate;
                    $this->mails[] = $mail2;
                    
                }
                $con->disconnect();
                
                return $this->mails;
            }
            catch(\Throwable $th)
            {
                return new EmailResponse(false,'mail Not Receive',$dsn->getEmail(),throwMessage:$th->getMessage());
            }
            
        }

        public function receive(Connexion $connexion)
        {   
            
            
            $dsn = $connexion->dsn;
            $folder = $connexion->folder;
            $criteria = $connexion->criteria;
            $imap = new Imap($dsn->getConnexion());

            $test = $imap->testConnection($dsn->getConnexionName());
            
            
            
            if(!$test) return new EmailResponse(false,'Connexion fail',$dsn->getEmail(),'');
            
            $con = $imap->get($dsn->getConnexionName());
         
            
            try
            {
                $mailIds = $con->switchMailbox($folder)->sortMails(searchCriteria:$criteria);
             
                
                if($mailIds == null) return null;
                foreach($mailIds as $id)
                {
                    $mail2 = new Ma;
                    $mail = $con->getMail($id);
    
                    $mail2->setMailId($mail->id);
                    $mail2->setFolder($mail->mailboxFolder);
                    $mail2->setFromAddress($mail->fromAddress);
                    $mail2->setSubject($mail->subject);
                    $mail2->setDate($mail->date);
                    $mail2->setToAddress($dsn->getEmail());
                    $mail2->setDsn($dsn);
                    $mail2->setTextHtml($mail->textHtml);
                    $mail2->setTextPlain($mail->textPlain);

                    $this->mails[] = $mail2;
                    
                }
                $con->disconnect();
                return $this->mails;
            }
            catch(\Throwable $th)
            {
                return new EmailResponse(succes:false,message:'mail Not Receive',sender:$dsn->getEmail(),throwMessage:$th->getMessage());
            }            

        }


        
    }   