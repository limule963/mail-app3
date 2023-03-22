<?php

namespace App\Listener;

use App\Event\MailPreRenderEvent;

class MailPreRenderListener 
{



    public function onMailPreRender(MailPreRenderEvent $event)
    {
        $variables = $event->getVariables();
        $template = $event->getTemplate();

        dd($variables,$template);
    }
    
}