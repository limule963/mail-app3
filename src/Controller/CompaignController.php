<?php
    
    namespace App\Controller;

use App\Entity\Compaign;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

    class CompaignController extends AbstractController
    {

        public function __construct(private CrudControllerHelpers $crud)
        {
            
        }
        
        #[Route('app/compaign',name:'app_compaign')]
        public function index()
        {
            return $this->render('Email/compaign.html.twig',[]);
        }


        #[Route('app/compaign/delete/{id}',name:'app_compaign_delete')]
        public function deleteCompaign(Compaign $compaign)
        {

            $this->crud->deleteCompaign($compaign);
            $this->addFlash('success', 'compaign deleted');
            return $this->redirectToRoute('app_compaign');
        }

        #[Route('app/compaign/add/',name:'app_compaign_add')]
        public function addCompaign(Compaign $compaign)
        {

            $compaign = new Compaign;
        }

        
        
    }