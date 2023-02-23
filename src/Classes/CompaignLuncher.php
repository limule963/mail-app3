<?php
namespace App\Classes;

use App\Data\Sequence;
use App\Entity\Compaign;

    class CompaignLuncher
    {

        private Sequence $sequence;
        public function __construct(private Sequencer $sequencer,private SequenceLuncher $sequenceLuncher)
        {
            
        }
        
        public function sequence(Compaign $compaign):self
        {
            $this->sequencer->sequence($compaign);
            return $this;
        }

        public function lunch()
        {
            $this->sequenceLuncher->lunch($this->sequence);
        }
    }