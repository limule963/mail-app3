<?php
    namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

    class AppController extends AbstractController
    {

        public function __construct(private CrudControllerHelpers $crud)
        {
            
        }
        #[Route('/app',name:'app_app')]
        public function index(Request $request)
        {



            $items = null;

            if($request->request->get('search') != null)
            {
                $search = $request->request->get('search');
                $items = $this->search($search);
            }



            return $this->render('Email/email.html.twig',[

            ]);
        }

        private function search($search)
        {
            
            return $this->crud->searchCompaigns(($this->getUser())->getId(),$search);
        }
        
        

    }