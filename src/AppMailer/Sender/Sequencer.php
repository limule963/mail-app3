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
        // /**@var Step */
        // private $step;

        /**@var Schedule */
        private $schedule;

        // /**
        //  * @var Step[] $steps
        //  *
        //  */
        // private $steps;
        
        private $compaignId;

        public function __construct( private CrudControllerHelpers $crud,private BodyRendererInterface $bodyRenderer)
        {
            
        }
        
        
        public function sequence(Compaign $compaign):?Sequence
        {
            $steps = $compaign->getSteps()->getValues();
            
            
            // $this->steps = $steps;
            $this->schedule = $compaign->getSchedule();
            $this->compaignId = $compaign->getId();

            
            // if($compaign->newStepPriority)
            // {
                //     $steps = array_reverse($steps);
                // }  
            
            $this->stepsStatusActivator($steps);
            $steps =  $this->stepsStatusCompleter($steps,$compaign->newStepPriority);
            
            


            if($this->isAllStepsComplete($steps))
            {
                $status = $this->crud->getStatus(STAT::COMPAIGN_COMPLETE);
                $compaign->setStatus($status);
                $this->crud->saveCompaign($compaign);
                return null;
            }
            else
            {
                $step = $this->stepSender($steps);
                if($step != null) return new Sequence($step);
                return null;
            }


            // foreach($steps as $step)
            // {
                // $lead = $this->crud->getLeadsByStatus($compaign->getId(),$step->getStatus()->getStatus(),1);
                // if(empty($lead)) continue;


            //     if($step->getStatus()->getStatus() == STAT::STEP_COMPLETE) continue;
            //     if(!$this->isStepDoJob($step)) continue;

            //     else
            //     {
            //         $this->step = $step;
            //         // $sequenceState =STAT::SEQUENCE_ONHOLD;
            //         return new Sequence($step);
            //         break;
            //     }
                
            // }

            // $status = $this->crud->getStatus(STAT::COMPAIGN_COMPLETE);
            // $compaign->setStatus($status);
            // $this->crud->saveCompaign($compaign);

            // return null;
            
        }
        
        /**@param Step[] $steps */
        private function isAllStepsComplete($steps):bool
        {
            foreach($steps as $step)
            {
                if($step->getStatus()->getStatus() == STAT::STEP_COMPLETE) continue;
                return false;

            }
            return true;
        }

        private function isStepDoJob(Step $step)
        {
            $lead = $this->crud->getLeadsByStep($this->compaignId,$step->getId(),1);
            // $active = $this->isStepActive($step);
            if(empty($lead)) return false;
            return true;

        }

        /**@param Step[] $steps */
        private function stepsStatusCompleter(array $steps,bool $newStepPriority)
        {
            foreach ($steps as $key => $step)
            {
                $stepStatus = $step->getStatus()->getStatus();
                // if( $stepStatus ==  STAT::STEP_COMPLETE) continue;
                if( $stepStatus !=  STAT::STEP_ACTIVE) continue;
                
                $doJob = $this->isStepDoJob($step);
                
                if($key == 0)
                {
                    if($doJob == false)
                    {

                        $Status = $this->crud->getStatus(STAT::STEP_COMPLETE);
                        $step->setStatus($Status);
                        $this->crud->saveStep($step);
                    }

                    // continue;
                }
                else
                {

                    $key--;
    
                    if($steps[$key]->getStatus()->getStatus() == STAT::STEP_COMPLETE && $doJob == false) 
                    {
                        $Status = $this->crud->getStatus(STAT::STEP_COMPLETE);
                        $step->setStatus($Status);
                        $this->crud->saveStep($step);
                    }
                }
                
                // if($newStepPriority == true) $key++;
                // else $key--;

                
            }

            if($newStepPriority) return array_reverse($steps);
            else return $steps;


        }


        private function stepSender($steps)
        {
            foreach($steps as $step)
            {
                $status = $step->getStatus()->getStatus();
                $doJob = $this->isStepDoJob($step);
                $stepToSend = null;
                if($status == STAT::STEP_ACTIVE && $doJob == true)
                {
                    $stepToSend = $step;
                    break;
                }

            }
            return $stepToSend;
            
        }


        /**@param Step[] $steps */
        private function stepsStatusActivator($steps)
        {

            $schedule = $this->schedule;
            
            
            foreach($steps as $step)
            {
                if($step->getStatus()->getStatus() == STAT::STEP_ACTIVE) continue;
                if($step->getStatus()->getStatus() == STAT::STEP_COMPLETE) continue;

                $startTime =$schedule->getStartTime()->getTimestamp() + $step->dayAfterLastStep*86400;
                
                //dd($startTime,time(),$step);
                
                if(time() > $startTime)
                {
                    $status = $this->crud->getStatus(STAT::STEP_ACTIVE);
                    $step->setStatus($status);
                    $this->crud->saveStep($step,false);
                }
            }
            $this->crud->em>flush();

        }
        





        /**step est active si son attribut startTime est inferieur au time actuel */

        /**@param Step $step */
        // private function isStepActive(Step $step):bool
        // {
        //     $schedule = $this->schedule;

        //     $startTime =$schedule->getStartTime()->getTimestamp() + $step->dayAfterLastStep*3600;
        //     // dd($startTime,time());

        //      if(time() > $startTime) return true;
        //     return false;
        // }


    
    }