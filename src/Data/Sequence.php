<?php

namespace App\Data;

use App\Entity\Email;

    class Sequence
    {
        public function __construct(
            public Email $email,
            public array $dsns,
            public array $leadStatusTable,
            public int $compaignId,
            public string $stepStatus

            )
        {
            
        }

        private function contains($tab,$item)
        {
            foreach ($tab as $key => $value) {
                if($item===$value) return $key;
            }
            return false;
        }
    
        public function getNextLeadStatus($lastStatus):string
        {   
            // if($lastStatus ='') return STAT::LEAD_STEP_1;
            $tab =$this->leadStatusTable;
            $count = count($tab);
            $key=$this->contains($tab,$lastStatus);
            
            $key++;
    
            if($key >= $count ) return STATUS::LEAD_COMPLETE;
            return $tab[$key];
    
    
        }
    }