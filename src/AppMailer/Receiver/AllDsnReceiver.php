<?php

namespace App\AppMailer\Receiver;
use App\Entity\Dsn;

    class AllDsnReceiver
    {
        public $mails;
        public function __construct(private AllFolderReceiver $allrec)
        {
            
        }

        /**@param Dsn[] $dsns */
        public function getMails(array $dsns)
        {
            foreach($dsns as $dsn)
            {
                $this->mails[$dsn->getEmail()] = $this->allrec->receive($dsn);
            }

            return $this->mails;
        }
    }