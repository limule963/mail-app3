<?php

namespace App\AppMailer\Data;



    class CompaignResponse
    {
        public array $stat;

        public function setResponse(?EmailResponse $em):self
        {
            if($em != null)
            {
                $this->stat[] = $em;
            }
            return $this;

        }
        public function setCompagneState($compaignState):self
        {
            $this->stat['compaignStatus'] = $compaignState;
            return $this;
        }

        
    }