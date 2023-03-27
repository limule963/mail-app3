<?php
    namespace App\AppMailer\Receiver;

use App\Entity\Dsn;
use App\Entity\Compaign;
use App\AppMailer\Data\MailData;
use Doctrine\Common\Collections\Criteria;

    class CompaignMailSaver
    {   
        // /**@var Dsn[] */
        // private $dsns;
        // private int $compaignId;
        // private \DateTimeImmutable $compaignStartTime;

        public function __construct(private MailSaver $ms,private MailData $md)
        {
            
        }

        /**@param ?Dsn[] $dsns */
        public function save( Compaign $compaign, mixed $criteria = 1)
        {
        
        
            $dsns = $compaign->getDsns()->getValues();

            $compaignId = $compaign->getId();
            $compaignStartTime = $compaign->getSchedule()->getStartTime();
            

            foreach($dsns as $key => $dsn)
            {

                $mailData = $this->md->set($dsn,$compaignId,$criteria,$compaignStartTime);
                
                $this->ms->saveMails($mailData);
            //   if($key == 1) dd($dsn);
                
            }
    
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    