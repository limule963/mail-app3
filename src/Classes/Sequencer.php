<?php

namespace App\Classes;

use App\Entity\Dsn;
use App\Controller\CrudControllerHelpers;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

    class Sequencer
    {
        private $email;
        private $dsn;
        private $leads;
        private $step;
        
        public function __construct( private CrudControllerHelpers $crud)
        {
            
        }
        
        /**
         * @var Dsn[] $dsn
         */
        public function prepare(array $steps = null, $dsn = null)
        {
            
            foreach($steps as $step)
            {
                
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

        private function getTemplatedEmail($email)
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

        public function getLeads(array $lead = null)
        {
            return $this->leads;
        }

        public function getDsn():Dsn
        {
            return $this->dsn;
        }

        public function getEmail()
        {
            return $this->email;
        }



        private function contains($tab,$item)
        {
            foreach ($tab as $key => $value) {
                if($item===$value) return $key;
            }
            return false;
        }
    
        private function getNextLeadStatus($tab,$lastStatus)
        {
            $count = count($tab);
            $key=$this->contains($tab,$lastStatus);
            
            $key++;
    
            if($key >= $count ) return 'lead.complete';
            return $tab[$key];
    
    
        }

        /**step est active si son attribut startTime est inferieur au time actuel */
        private function isStepActive($step):bool
        {
            $startTime = gmmktime($step->startTime);
            if(time() > $startTime) return true;
            return false;
        }

    
    }