<?php

namespace App\Classes;

use App\Entity\Lead;
use App\Entity\Step;
use App\Entity\Compaign;
use App\Entity\Email as Em;
use App\Data\STATUS as STAT;
use Symfony\Component\Mime\Email;
use App\Controller\CrudControllerHelpers;
use App\Data\Sequence;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

    class Sequencer
    {
        /**@var Email */
        private $email;

        /**@var Lead[] */
        private $leads;
        
        /**@var Step */
        private $step;

        private $schedule;

        /**
         * @var Step[] $steps
         *
         */
        private $steps;
        
        public function __construct( private CrudControllerHelpers $crud)
        {
            
        }
        
        
        public function sequence(Compaign $compaign):Sequence
        {
            $steps = $compaign->getSteps()->getValues();
            $this->steps = $steps;
            $dsns = $compaign->getDsns()->getValues();
            $this->schedule = $compaign->getSchedule();


            if($compaign->newStepPriority) array_reverse($steps);

            foreach($steps as $step)
            {
                /**@var Step $step */
                if( !$this->isStepActive($step)) continue;
                else
                {
                    $this->leads = $this->crud->getLeadsByStatus($step->leadStatus,count($dsns));
                    if(empty($this->leads)) continue;
                    else
                    {

                        $this->step = $step;
                        $sequenceState =STAT::SEQUENCE_ONHOLD;
                        break;
                    }
                }
            }
            $sequenceState =STAT::SEQUENCE_COMPLETE;

            $this->email = $this->getTemplatedEmail($this->step->getEmail());
        

            return new Sequence(
                $this->leads,
                $this->email,
                $compaign->getDsns()->getValues(),
                $sequenceState,
                $this->leadStatusTable($this->steps),
                $compaign->getId(),
                $this->step->leadStatus
            );
 
        }
        

        /**@var Step[] $steps */
        private function leadStatusTable($steps)
        {
            foreach ($steps as $step) {
                /**@var Step $step */
                $statusTable[] = $step->leadStatus;
            } 
                
            return $statusTable;
        }


        private function getTemplatedEmail(Em $email):Email
        {
            $subject = $email->getSubject();
            $emailLink = $email->getEmailLink();


            return $email = (new TemplatedEmail())
                // ->to(new Address('ryan@example.com'))
                ->subject($subject)

                // path of the Twig template to render
                ->htmlTemplate($emailLink)

                // pass variables (name => value) to the template
                ->context([
                    'username' => 'foo',
                ])
            ;
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