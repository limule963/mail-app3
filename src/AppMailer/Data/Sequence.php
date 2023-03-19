<?php

namespace App\AppMailer\Data;


use App\Entity\Email;
use App\Entity\Step;

    class Sequence
    {
        public string $sequenceStatus;
        public ?Email $email;
        public ?array $dsns;
        public ?array $leadStatusTable;
        public ?int $compaignId;
        public ?string $stepStatus;
        public ?string $compaignState;
        public ?bool $tracker;

        public ?Step $step;

        public function __construct(Step $step)
        {
            $this->step = $step;
            $this->sequenceStatus = STATUS::SEQUENCE_ONHOLD;
            $this->email = $step->getEmail();
            $this->dsns = $step->getCompaign()->getDsns()->getValues();
            $this->compaignId = $step->getCompaign()->getId();
            $this->stepStatus = $step->getStatus()->getStatus();
            $this->compaignState = $step->getCompaign()->getStatus()->getStatus();
            $this->tracker = $step->getCompaign()->isIsTracker();
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
            $tab =$this->leadStatusTable($this->step->getCompaign()->getSteps()->getValues());
            $count = count($tab);
            $key=$this->contains($tab,$lastStatus);
            
            $key++;
    
            if($key >= $count ) return STATUS::LEAD_COMPLETE;
            return $tab[$key];
    
    
        }

        /**@var Step[] $steps */
        private function leadStatusTable($steps)
        {
            foreach ($steps as $step) {
                /**@var Step $step */
                $statusTable[] = $step->getStatus()->getStatus();
            } 
                
            return $statusTable;
        }
    }