<?php

namespace App\Controller;

use App\Entity\Lead;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\AppMailer\Data\TransparentPixelResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrackingController extends AbstractController
{
    public function __construct(private CrudControllerHelpers $crud)
    {
        
    }

    #[Route('/track.gif', name: 'app_tracking')]
    public function index(Request $request): Response
    {
        // dd(new TransparentPixelResponse);
        
        $id = $request->query->get('id');
        // dd($id);
        if (null !== $id) {
            //... executes some logic to retrieve the email and mark it as opened

            $lead = $this->crud->getLead($id);
            if($lead != null) $this->crud->saveLead($lead);
        }
        return new TransparentPixelResponse();
        // return $this->render('tracking/index.html.twig', [
        //     'controller_name' => 'TrackingController',
        // ]);
    }

    #[Route('/image/{id}/papillon.png',name:'app_tracking_2')]
    public function tracker(Lead $lead)
    {
        //making change for 
        $leadName = $lead->getName();
        $lead->setName($leadName."1");
        $this->crud->saveLead($lead);
        

        return $this->redirect("https://op.clemaos.com/Public/images/image.png");


    }
}
