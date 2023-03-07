<?php

namespace App\AppMailer\Sender;


use App\Entity\Step;
use App\Entity\Compaign;
use App\Entity\Schedule;
use App\AppMailer\Data\STATUS as STAT;
use App\AppMailer\Data\Sequence;
use App\Controller\CrudControllerHelpers;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Mime\BodyRendererInterface;

    class Sequencer
    {      
        /**@var Step */
        private $step;

        /**@var Schedule */
        private $schedule;

        /**
         * @var Step[] $steps
         *
         */
        private $steps;
        
        private $compaignId;

        public function __construct( private CrudControllerHelpers $crud,private BodyRendererInterface $bodyRenderer)
        {
            
        }
        
        
        public function sequence(Compaign $compaign):Sequence|null
        {
            $steps = $compaign->getSteps()->getValues();
            $this->steps = $steps;
            $this->schedule = $compaign->getSchedule();
            $this->compaignId = $compaign->getId();

            
            // if($compaign->newStepPriority)
            // {
            //     $steps = array_reverse($steps);
            // }  

            $steps =  $this->stepManager($steps,$compaign->newStepPriority);

            foreach($steps as $step)
            {
                // $lead = $this->crud->getLeadsByStatus($compaign->getId(),$step->getStatus()->getStatus(),1);
                // if(empty($lead)) continue;
                if($step->stepState == STAT::STEP_COMPLETE) continue;
                if(!$this->isStepDoJob($step)) continue;
                else
                {
                    $this->step = $step;
                    // $sequenceState =STAT::SEQUENCE_ONHOLD;
                    return new Sequence(
                            email:$step->getEmail(),
                            dsns:$compaign->getDsns()->getValues(),
                            leadStatusTable:$this->leadStatusTable($this->steps),
                            compaignId:$compaign->getId(),
                            stepStatus:$step->getStatus()->getStatus(),
                            compaignState:$compaign->getStatus()->getStatus(),
                            tracker:$compaign->isIsTracker()
                        );
                    break;
                }
                
            }

            $status = $this->crud->getStatus(STAT::COMPAIGN_COMPLETE);
            $compaign->setStatus($status);
            $this->crud->saveCompaign($compaign);

            return null;
            
        


 
        }
        

        private function isStepDoJob(Step $step)
        {
            $lead = $this->crud->getLeadsByStatus($this->compaignId,$step->getStatus()->getStatus(),1);
            $active = $this->isStepActive($step);
            if(empty($lead) && $active == true) return false;
            return true;

        }

        /**@param Step[] $steps */
        private function stepManager(array $steps,bool $newStepPriority)
        {
            foreach ($steps as $key => $step)
            {
                if($step->stepState == STAT::STEP_COMPLETE) continue;
                
                $doJob = $this->isStepDoJob($step);
                
                if($step->getStatus()->getStatus()==STAT::STEP_1 && $doJob == false)
                {
                    $step->stepState = STAT::STEP_COMPLETE;
                    $this->crud->saveStep($step);
                    continue;
                }
                if($key == 0) continue;
                
                if($newStepPriority == true) $key++;
                else $key--;
                if($steps[$key]->stepState == STAT::STEP_COMPLETE && $doJob == false) 
                {
                    $step->stepState = STAT::STEP_COMPLETE;
                    $this->crud->saveStep($step);
                }

                
            }

            if($newStepPriority) return array_reverse($steps);
            else return $steps;
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






        /**step est active si son attribut startTime est inferieur au time actuel */

        /**@param Step $step */
        private function isStepActive(Step $step):bool
        {
            $schedule = $this->schedule;

            $startTime =$schedule->getStartTime()->getTimestamp() + $step->dayAfterLastStep*3600;
            // dd($startTime,time());

             if(time() > $startTime) return true;
            return false;
        }

    
    }