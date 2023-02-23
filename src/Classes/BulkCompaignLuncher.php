<?php

namespace App\Classes;

use App\Data\STATUS as STAT;
use App\Entity\Compaign;

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