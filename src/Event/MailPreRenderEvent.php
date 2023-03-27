<?php
    
    namespace App\Event;

use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\Event;

    class MailPreRenderEvent extends Event
    {


        public const NANE = 'mail.prerender';

        private $variables;
        private $template;
        private Email $email;



        public function __construct($variables,$template,Email $email)
        {
            $this->variables = $variables;
            $this->template = $template;
            $this->email = $email;
        }

        public function setVariables($variables)
        {
            $this->variables = $variables;
        }
        
        public function setTemplate($template)
        {
            $this->template = $template;
        }
        
        
        public function getTemplate()
        {
            return $this->template;
        }
        
        public function getVariables()
        {
            return $this->variables;
        }
        public function getEmail()
        {
            return $this->email;
        }
        
    }