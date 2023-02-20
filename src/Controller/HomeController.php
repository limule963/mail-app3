<?php

namespace App\Controller;

use App\Classes\CompaignLuncher;
use App\Classes\EmailSender;
use App\Classes\SequenceLuncher;
use App\Classes\Sequencer;
use App\Classes\SimpleObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CompaignLuncher $sender): Response
    {

        
        // $tab = null;
        // for($i = 0; $i<10; $i++)
        // {
        //     $tab[] = new SimpleObject('koff'.$i, $i);
        // }

        // $cmp = function(string $a,string $b){
        //     if($a == $b) return 0;
        //     return ($a<$b) ? 1: -1;
        // };
        // usort($tab,$cmp);
        // dd($tab);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
