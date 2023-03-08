<?php
    
    namespace App\Controller;

use App\AppMailer\Data\STATUS;
use App\AppMailer\Data\TransparentPixelResponse;
use App\AppMailer\Sender\CompaignLuncher;
use App\Entity\Compaign;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        public function addCompaign(Request $request)
        {
            $name = $request->request->get('name');
            $this->crud->addCompaign($this->getUser()->getId(),$name);

            return $this->redirectToRoute('app_compaign');
        }
        #[Route('/app/compaign/lunch/{id}',name:'app_compaign_lunch')]
        public function lunchCompaign(Compaign $compaign,CompaignLuncher $cl)
        {
            $Status = $this->crud->getStatus(STATUS::COMPAIGN_ACTIVE);
            $compaign->setStatus($Status);

            //appel le cron sur un lien lanceur
            //Quelques codes
            $res = [
                'response'=>true,
                'message'=> 'compaign lunched successfully'
            ];
            return $this->json($res);

        }
        
        
        #[Route('/app/compaign/pause/{id}',name:'app_compaign_pause')]
        public function pauseCompaign(Compaign $compaign,CompaignLuncher $cl)
        {
            $Status = $this->crud->getStatus(STATUS::COMPAIGN_ACTIVE);
            $compaign->setStatus($Status);

            //appel le cron sur un lien lanceur
            //Quelques codes
            $res = [
                'response'=>true,
                'message'=> 'compaign paused successfully'
            ];
            return $this->json($res);

        }
        #[Route('/app/compaign/detail/{id}',name:'app_compaign_detail')]
        public function compaignDetail(Compaign $compaign)
        {
            return $this->render('Email/compaign_detail.html.twig');
        }
        




    }