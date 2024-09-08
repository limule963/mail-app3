<?php

namespace App\AppMailer\Sender;

use App\AppMailer\Data\STATUS;
use App\AppMailer\Receiver\CompaignMailSaver;
use App\Entity\Compaign;



    class BulkCompaignLuncher
    {
        public function __construct(private CompaignLuncher $compaignLuncher)
        {
            
        }

        /**@param Compaign[] $compaigns */
        public function lunch(?array $compaigns)
        {
            if($compaigns == null) return null;
            foreach($compaigns as $compaign) 
            {
                $status = $compaign->getStatus()->getStatus();
                
                
                if($status != STATUS::COMPAIGN_ACTIVE ) continue;
                
                //dd($status,$compaign);
                $this->compaignLuncher->sequence($compaign)->lunch();
            }
        }
}