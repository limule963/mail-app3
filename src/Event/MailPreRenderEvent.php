<?php
    
    namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

    class MailPreRenderEvent extends Event
    {


        public const NANE = 'mail.prerender';

        private $variables;
        private $template;



        public function __construct($variables,$template)
        {
            $this->variables = $variables;
            $this->template = $template;
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
        
    }