<?php

namespace App\AppMailer\Receiver;

use App\Entity\Compaign;

    class BulkCompaignMailSaver
    {
        public function __construct(private CompaignMailSaver $cms)
        {
            
        }

        /**@param Compaign[] $compaigns */
        public function saveall($compaigns)
        {
            foreach($compaigns as $compaign)
            {

                $this->cms->save($compaign);
            }
        }
        
        
    }
