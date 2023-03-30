<?php
    namespace App\AppMailer\Receiver;

use App\Entity\Dsn;
use App\AppMailer\Data\FOLDER;
use App\AppMailer\Data\Connexion;
use App\AppMailer\Data\EmailResponse;

    class AllFolderReceiver
    {
        private $folders =[
            FOLDER::INBOX,
            FOLDER::JUNK,

        ] ;
        private $mails;

        public function __construct(public Connexion $connex)
        {
        }

        public function receive(Dsn $dsn,\DateTimeImmutable $startTime,mixed $criteria='',)
        {
            if($criteria == '') $criteria = $this->getCriteria($startTime);
            if($criteria == 1) $criteria = $this->getCriteria($startTime);

            foreach($this->folders as $key => $folder)
            {
               
                $mails = (new Receiver)->receive( $this->connex->set($dsn,$criteria,$folder)); 
                if($mails == null) continue;
                if($mails instanceof EmailResponse) return $mails;
                $this->mails[$folder]=$mails;       
                $mails = null;
                
            }
            return $this->mails;
        }

        private function getCriteria(\DateTimeImmutable $startTime)
        {

            $stamp = $startTime->getTimestamp();
            $date =getdate($stamp);
            $criteria = 'SUBJECT Re '
                        .'SINCE '.$date['year'].'-'.$date['mon'].'-'.$date['mday'].' '
                        // .'FROM '.$dsn->getEmail();
                     ;   
                        
            return $criteria;            
        }


    }