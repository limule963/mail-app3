<?php

namespace App\Classes;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

    class Sequencer
    {
        private $email;
        private $dsn;
        private $leads;
        private $step;
        private $crud;
        public function __construct()
        {
            
        }
        
        public function prepare(array $steps = null,/*Dsn*/ $dsn = null)
        {

            foreach($steps as $step)
            {
                if( $this->isStepActive($step))
                {
                     $this->step = $step;
                     break;
                }
            }

            $this->dsn = $dsn;
            $this->email = $this->getTemplatedEmail($this->step->getEmail());
            
            $this->leads = $this->crud->getLeadsByStatus();

            
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

        public function getDsn()
        {
            return $this->dsn;
        }

        public function getEmail()
        {
            return $this->email;
        }

        private function setEmail(string $email)
        {

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
        private function isStepActive($step):bool
        {
            $startTime = gmmktime($step->startTime);
 
            return false;
        }

    
    }