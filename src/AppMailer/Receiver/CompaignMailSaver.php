<?php
    namespace App\AppMailer\Receiver;

use App\Entity\Dsn;
use App\AppMailer\Data\MailData;
use Doctrine\Common\Collections\Criteria;

    class CompaignMailSaver
    {
        public function __construct(private MailSaver $ms,private MailData $md)
        {
            
        }

        /**@param ?Dsn[] $dsns */
        public function save( $dsns, int $compaignId, mixed $criteria,\DateTimeImmutable $compaignStartTime)
        {
            foreach($dsns as $dsn)
            {

                $mailData = $this->md->set($dsn,$compaignId,$criteria,$compaignStartTime);
                $this->ms->saveMails($mailData);
            }
        }
    }