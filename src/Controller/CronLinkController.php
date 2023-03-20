<?php
    namespace App\Controller;

use App\AppMailer\Data\STATUS;
use App\AppMailer\Receiver\CompaignMailSaver;
use App\AppMailer\Sender\CompaignLuncher;
use App\Entity\Compaign;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

    class CronLinkController extends AbstractController
    {
        
        #[Route(path:"Compaign-lunch/{id}/cronjob",name:"app_compaign_cronjob")]
        public function index(Compaign $compaign,CompaignLuncher $cl,CompaignMailSaver $cms)
        {
            if($compaign == null || $compaign->getStatus()->getStatus() != STATUS::COMPAIGN_ACTIVE) return null;

            $date = getdate(time());
            $h = $date['hours'];
            $from = $compaign->getSchedule()->getFromm();
            $to = $compaign->getSchedule()->getToo();

            if($h < $from || $h > $to)
            {
                $dsns = $compaign->getDsns()->getValues();
                $compaignId = $compaign->getId();
                $compaignStartTime = $compaign->getSchedule()->getStartTime();


    
                $cms->save($dsns,$compaignId,1,$compaignStartTime);
                return null;
            } 


            $cl->sequence($compaign)->lunch();
        }
        
    }