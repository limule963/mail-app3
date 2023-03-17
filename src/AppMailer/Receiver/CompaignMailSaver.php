<?php
    namespace App\AppMailer\Receiver;

use App\AppMailer\Data\MailData;
use Doctrine\Common\Collections\Criteria;

    class CompaignMailSaver
    {
        public function __construct(private MailSaver $ms,private MailData $md)
        {
            
        }

        /**@param ?Dsn[] */
        public function save( $dsns,$compaignId,$criteria,$compaignStartTime)
        {
            foreach($dsns as $dsn)
            {

                $mailData = $this->md->set($dsn,$compaignId,$criteria,$compaignStartTime);
                $this->ms->saveMails($mailData);
            }
        }
    }