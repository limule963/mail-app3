<?php

namespace App\AppMailer\Receiver;

use App\AppMailer\Data\Connexion;
use App\AppMailer\Data\EmailResponse;
use App\AppMailer\Data\Mail;
use SecIT\ImapBundle\Service\Imap;


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
                    // if($mail == null) continue;
                    $mail2->subject = $mail->subject;
                    $mail2->from = $mail->fromAddress;
                    $mail2->to = $mail->toString;
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


        
    }   