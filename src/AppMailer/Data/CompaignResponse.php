<?php

namespace App\AppMailer\Data;



    class CompaignResponse
    {
        public array $stat;

        public function setStat(?EmailResponse $em,$compaignState)
        {
            if($em != null)
            {
                $this->stat[] = $em;
                $this->stat['compaignStatus'] = $compaignState;
            }
            else 
            $this->stat['compaignStatus'] = $compaignState;

        }
    }