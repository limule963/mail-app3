<?php
namespace App\Classes;

use App\Data\Sequence;
use App\Data\STATUS;
use App\Entity\Compaign;

    class CompaignLuncher
    {

        private Sequence $sequence;
        private $compaignStatus;
        private $fromm;
        private $too;
        public function __construct(private Sequencer $sequencer,private SequenceLuncher $sequenceLuncher)
        {
            
        }
        
        public function sequence(Compaign $compaign):self
        {
            $this->sequence = $this->sequencer->sequence($compaign);
            $this->compaignStatus = $compaign->getStatus()->getStatus();
            $this->fromm = $compaign->getSchedule()->getFromm();
            $this->too = $compaign->getSchedule()->getToo();
            return $this;
        }
        
        public function lunch()
        {
            if($this->compaignStatus != STATUS::COMPAIGN_ACTIVE) return ;
            if(getdate('hours')<$this->fromm && getdate('hours')>$this->too) return;
            return $this->sequenceLuncher->lunch($this->sequence);
        }
    }