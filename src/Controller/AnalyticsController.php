<?php
    namespace App\Controller;

use App\Entity\Compaign;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    class AnalyticsController extends AbstractController
    {

        #[Route(path:"/compaign/{id}/analytics",name:"app_compaign_analytics")]
        public function compaignAnalytics(Compaign $compaign)
        {
            return $this-> render('Email/compaign_analytics.html.twig',[
                'compaign'=>$compaign
            ]);
        }
        
    }