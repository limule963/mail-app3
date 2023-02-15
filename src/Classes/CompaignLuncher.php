<?php
    namespace App\Classes;

    class CompaignLuncher
    {
        private $compaign;
        private $step;
        private $lead;
        private $dsn;

        public function __construct(private Sequencer $sequencer,private SequenceLuncher $sequenceLuncher)
        {
            
        }

        public function prepare(/*Compaign*/ $compaign):self
        {
            $this->compaign = $compaign;
            $this->lead = $compaign->getLead();
            $this->step = $compaign->getStep();

            return $this;
        }

        public function sequence():self
        {
            $this->sequencer->prepare($this->step,$this->dsn);
            $this->sequencer->sequence($this->lead);

            return $this;

        }

        public function lunch():self
        {
            $this->sequenceLuncher->prepare($this->sequencer);
            $this->sequenceLuncher->lunch();

            
            
            return $this;
        }
    }