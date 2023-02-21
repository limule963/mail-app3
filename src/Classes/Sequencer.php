<?php

namespace App\Classes;

use App\Entity\Email as Em;
use App\Entity\Dsn;
use App\Entity\Lead;
use App\Entity\Step;
use App\Data\STATUS as STAT;
use Symfony\Component\Mime\Email;
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

        private $count;
        public function __construct( private CrudControllerHelpers $crud)
        {
            
        }
        
        /**
         * @var Dsn[] $dsn
         * @var Step[] $steps
         *
         */
        private $steps;
        public function prepare(array $steps = null,  $dsn = null,$newStepPriority = true)
        {
            $this->steps = $steps;

            if($newStepPriority) array_reverse($steps);
            foreach($steps as $step)
            {
                /**@var Step $step */
                if( !$this->isStepActive($step)) continue;
                {
                    $this->leads = $this->crud->getLeadsByStatus($step->leadStatus);
                    if(empty($this->leads)) continue;
                    else
                    {

                        $this->step = $step;
                        break;
                    }
                }
            }

            

            $this->dsn = $dsn;
            $this->email = $this->getTemplatedEmail($this->step->getEmail());
            
        }

        /**@var Step[] $steps */
        private function leadStatusTable($steps)
        {
            foreach ($steps as $key => $step) {
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
        private function setLeadstatus()
        {

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
        public function sequence(array $leads)
        {
            
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
        private function isStepActive($step):bool
        {
            $startTime = gmmktime($step->startTime);
            if(time() > $startTime) return true;
            return false;
        }

    
    }