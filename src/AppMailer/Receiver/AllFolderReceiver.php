<?php
    namespace App\AppMailer\Receiver;

use App\Entity\Dsn;
use App\AppMailer\Data\FOLDER;
use App\AppMailer\Data\Connexion;

    class AllFolderReceiver
    {
        private $folders =[
            FOLDER::SENT,
            FOLDER::INBOX,

        ] ;
        private $mails;

        public function __construct(public Connexion $connex)
        {
        }

        public function receive(Dsn $dsn)
        {
            $criteria = $this->getCriteria($dsn);

            foreach($this->folders as $key => $folder)
            {
               
                $mails = (new Receiver)->getMail( $this->connex->set($dsn,$criteria,$folder)); 
                $this->mails[$folder]=$mails;       
                $mails = null;
                
            }

            return $this->mails;
        }

        private function getCriteria(Dsn $dsn)
        {
            $stamp = $dsn->getCreateAt()->getTimestamp();
            $date =getdate($stamp);
            return 'since '.$date['year'].'-'.$date['mon'].'-'.$date['mday'];           
        }


    }