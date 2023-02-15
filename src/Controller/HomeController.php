<?php

namespace App\Controller;

use App\Classes\SequenceLuncher;
use App\Classes\Sequencer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SequenceLuncher $sequencer): Response
    {

        dd($sequencer);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
