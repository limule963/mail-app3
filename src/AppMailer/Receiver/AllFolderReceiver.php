<?php
    namespace App\AppMailer\Receiver;

use App\Entity\Dsn;
use App\AppMailer\Data\FOLDER;
use App\AppMailer\Data\Connexion;
use App\AppMailer\Data\EmailResponse;

    class AllFolderReceiver
    {
        private $folders =[
            FOLDER::JUNK,
            FOLDER::INBOX,

        ] ;
        private $mails;

        public function __construct(public Connexion $connex)
        {
        }

        public function receive(Dsn $dsn,$criteria ='')
        {
            if ($criteria == '') $criteria = $this->getCriteria($dsn);

            foreach($this->folders as $key => $folder)
            {
               
                $mails = (new Receiver)->getMail( $this->connex->set($dsn,$criteria,$folder)); 
                if($mails instanceof EmailResponse) return $mails;
                $this->mails[$folder]=$mails;       
                $mails = null;
                
            }
            // foreach($this->mails as $mail)
            // {
            //     if($mail instanceof EmailResponse) return $mail;
            // }
            return $this->mails;
        }

        private function getCriteria(Dsn $dsn)
        {
            $stamp = $dsn->getCreateAt()->getTimestamp();
            $date =getdate($stamp);
            
            return 'since '.$date['year'].'-'.$date['mon'].'-'.$date['mday']; 

                    
        }


    }