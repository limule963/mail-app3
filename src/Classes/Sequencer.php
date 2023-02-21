<?php

namespace App\Classes;

use App\Entity\Dsn;
use App\Entity\Lead;
use Symfony\Component\Mime\Email;
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
        public function prepare(array $steps = null,  $dsn = null)
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
        private function leadStatusManager(array $steps)
        {
            foreach ($steps as $key => $value) {
                $status[] = 'lead.step.'.$key++;
            }
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