<?php

namespace App\AppMailer\Sender;



    class BulkCompaignLuncher
    {
        public function __construct(private CompaignLuncher $compaignLuncher)
        {
            
        }

        public function lunch(array $compaigns)
        {
            foreach($compaigns as $compaign) 
            {
                $this->compaignLuncher->lunch($compaign);
            }
        }
}