<?php

namespace App\Classes;

use App\Entity\Dsn;
use App\Entity\Lead;
use App\Entity\Step;
use App\Entity\Email as Em;
use App\Data\STATUS as STAT;
use Symfony\Component\Mime\Email;
use App\Repository\LeadRepository;
use App\Controller\CrudControllerHelpers;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

    class Sequencer
    {
        /**@var Email */
        private $email;

        /**@var Dsn[] */
        private $dsn;

        /**@var Lead[] */
        private $leads;
        
        /**@var Step */
        private $step;

        private $compaignId;
        public function __construct( private CrudControllerHelpers $crud)
        {
            
        }
        
        /**
         * @var Step[] $steps
         *
         */
        private $steps;
        /**
         * @param Step[] $steps
         * @param Dsn[] $dsns
         * @param int $compaignId
         * @param bool $newStepPriority
         */
        public function prepare(array $steps,array $dsns ,$compaignId,$newStepPriority = true )
        {
            $this->steps = $steps;

            if($newStepPriority) array_reverse($steps);

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
                        break;
                    }
                }
            }

            

            $this->dsn = $dsns;
            $this->email = $this->getTemplatedEmail($this->step->getEmail());
            $this->compaignId =$compaignId;
           
            
        }
        public function sequence()
        {

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
            $tab =$this->leadStatusTable($this->steps);
            $count = count($tab);
            $key=$this->contains($tab,$lastStatus);
            
            $key++;
    
            if($key >= $count ) return STAT::LEAD_COMPLETE;
            return $tab[$key];
    
    
        }

        private function getTemplatedEmail($email):Email
        {
            $sbject = $email->getSubject();
            $emailLink = $email->getEmailLink();


            return $email = (new TemplatedEmail())
                // ->to(new Address('ryan@example.com'))
                ->subject($sbject)

                // path of the Twig template to render
                ->htmlTemplate($emailLink)

                // pass variables (name => value) to the template
                ->context([
                    'username' => 'foo',
                ])
            ;
        }
        private function getLeadsByStatus($status)
        {
            return $leads = $this->crud->getLeadsByStatus($status);
        }
        
        /**@return Lead[] */
        public function getLeads()
        {
            return $this->leads;
        }

        /**@return Dsn[] */
        public function getDsns()
        {
            return $this->dsn;
        }

        public function getEmail():Email
        {
            return $this->email;
        }





        /**step est active si son attribut startTime est inferieur au time actuel */

        /**@param Step $step */
        private function isStepActive(Step $step):bool
        {
            $schedule = $this->crud->getSchedule($this->compaignId);

            $startTime =$schedule->getStartTime()->getTimestamp() + $step->dayAfterLastStep*3600;


            if(time() > $startTime) return true;
            return false;
        }

    
    }