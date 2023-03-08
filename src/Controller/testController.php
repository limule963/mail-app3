<?php
    namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

    class TestController extends AbstractController
    {
        #[Route('/boot',name:'app_boot')]
        public function index()
        {

            return $this->render('app/index.html.twig');
        }
    }