<?php

namespace App\Classes;

use App\Entity\Lead;
use App\Entity\Step;
use App\Data\Sequence;
use App\Entity\Compaign;
use App\Entity\Schedule;
use App\Entity\Email as Em;
use App\Data\STATUS as STAT;
use Symfony\Component\Mime\Email;
use App\Controller\CrudControllerHelpers;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
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
        
        public function __construct( private CrudControllerHelpers $crud,private BodyRendererInterface $bodyRenderer)
        {
            
        }
        
        
        public function sequence(Compaign $compaign):Sequence
        {
            $steps = $compaign->getSteps()->getValues();
            $this->steps = $steps;
            $this->schedule = $compaign->getSchedule();
            
            if($compaign->newStepPriority)  $steps = array_reverse($steps);

            foreach($steps as $step)
            {
                /**@var Step $step */
                if( !$this->isStepActive($step)) continue;
                else
                {
                    $lead = $this->crud->getLeadsByStatus($compaign->getId(),$step->getStatus()->getStatus(),1);
                    if(empty($lead)) continue;
                    else
                    {
                        $this->step = $step;
                        // $sequenceState =STAT::SEQUENCE_ONHOLD;
                        return new Sequence(
                                $this->step->getEmail(),
                                $compaign->getDsns()->getValues(),
                                $this->leadStatusTable($this->steps),
                                $compaign->getId(),
                                $this->step->getStatus()->getStatus()
                         );
                        break;
                    }
                }
            }
            // $sequenceState =STAT::SEQUENCE_COMPLETE;
            
        


 
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

             if(time() > $startTime) return true;
            return false;
        }

    
    }