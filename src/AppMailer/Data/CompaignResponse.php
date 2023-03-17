<?php

namespace App\AppMailer\Data;



    class CompaignResponse
    {
        public array $stat;
        private ?int $tms;
        private ?int $tmo;
        private ?int $tmr;
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
        public function setTms(?int $value):self
        {
            $this->tms = $value;
            return $this;
        }
        public function setTmo(?int $value):self
        {
            $this->tmo = $value;
            return $this;
        }
        public function setTmr(?int $value):self
        {
            $this->tmr = $value;
            return $this;
        }
        public function getTms()
        {
            return $this->tms;
        }
        public function getTmo()
        {
            return $this->tmo;
        }
        public function getTmr()
        {
            return $this->tmr;
        }
        
    }