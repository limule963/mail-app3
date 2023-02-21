<?php

namespace App\Classes;

use App\Data\STATUS as STAT;
use App\Entity\Compaign;

    class BulkCompaignLuncher
    {
        /**@var Compaign[] */
        private $compaigns;

 
        public function __construct(private CompaignLuncher $compaignLuncher)
        {
            
        }

        /**@param Compaign[] $compaigns */
        public function prepare($compaigns)
        {
            foreach($compaigns as $compaign)
            {
                if(($compaign->getStatus())->getStatus() == STAT::COMPAIGN_ACTIVE) $this->compaigns[] = $compaign;
            }
            
        }

        public function lunch()
        {
            foreach($this->compaigns as $compaign) $this->compaignLuncher->prepare($compaign)->sequence()->lunch();
        }
    }