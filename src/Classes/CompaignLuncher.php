<?php
namespace App\Classes;

use App\Entity\Step;
use App\Entity\Compaign;

    class CompaignLuncher
    {
        /**@var Compaign */
        private $compaign;
        /**@var Step[] */
        private $steps;
        /**
         * @var Dsn[]
         */
        private $dsns;

        public function __construct(private Sequencer $sequencer,private SequenceLuncher $sequenceLuncher)
        {
            
        }

        public function prepare(Compaign $compaign):self
        {
            $this->compaign = $compaign;
            $this->dsns = $compaign->getDsns();
            $this->steps = $compaign->getSteps();

            return $this;
        }

        public function sequence():self
        {
            $this->sequencer->prepare($this->steps,$this->dsns,$this->compaign->newStepPriority);

            return $this;

        }

        public function lunch():self
        {
            $this->sequenceLuncher->prepare($this->sequencer)->lunch();

            return $this;
        }
    }