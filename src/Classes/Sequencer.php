<?php
    namespace App\Classes;

    class Sequencer
    {
        private $email;
        private $dsn;
        private $lead;
        private $step;
        public function __construct()
        {
            
        }
        
        public function prepare(array $step = null,/*Dsn*/ $dsn = null)
        {


            /**code pour determiner le step a injecter*/

            /**Code pour  filtrer les leads pour l'etape actuelle*/
            $this->dsn = $dsn;
            $this->email = $this->step->getTemplatedEmail();
            
        }
        public function sequence(array $leads)
        {
            
        }

        public function getLeads(array $lead = null)
        {

        }
        public function getDsn()
        {

        }
        public function getEmail()
        {

        }
        private function setEmail(string $email)
        {

        }
        private function setDsn(string $dsn)
        {

        }
        private function setLead(string $lead)
        {

        }
    }