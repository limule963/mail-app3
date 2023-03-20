<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CronJobController extends AbstractController
{
    #[Route('/cron/job', name: 'app_cron_job')]
    public function index(): Response
    {
        return $this->render('cron_job/index.html.twig', [
            'controller_name' => 'CronJobController',
        ]);
    }

    #[Route(path:"/cronjob-dyd45ddiud8",name:"app_cronjob_1")]
    public function cron1()
    {
        
    }
    #[Route(path:"/cronjob-ddlo557iud7",name:"app_cronjob_2")]
    public function cron2()
    {
        
    }
    #[Route(path:"/cronjob-yd45dviu55",name:"app_cronjob_3")]
    public function cron3()
    {
        
    }
    #[Route(path:"/cronjob-d8d4545diud",name:"app_cronjob_3")]
    public function cron4()
    {
        
    }
    #[Route(path:"/cronjob-dyyy5dp8utb",name:"app_cronjob_5")]
    public function cron5()
    {
        
    }
    #[Route(path:"/cronjob-dydfeddnp4o",name:"app_cronjob_6")]
    public function cron6()
    {
        
    }
    #[Route(path:"/cronjob-mmd45ddgtdc",name:"app_cronjob_7")]
    public function cron7()
    {
        
    }
    #[Route(path:"/cronjob-dymt7ddivvp",name:"app_cronjob_8")]
    public function cron8()
    {
        
    }
    #[Route(path:"/cronjob-mad45ddiuiu",name:"app_cronjob_9")]
    public function cron9()
    {
        
    }
    #[Route(path:"/cronjob-dyd4j5tiud8",name:"app_cronjob_10")]
    public function cron10()
    {
        
    }

    
}
