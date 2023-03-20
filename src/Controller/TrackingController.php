<?php

namespace App\Controller;

use App\Entity\Lead;
use App\Entity\Step;
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
            //code for lead
            
            $compaign = $lead->getCompaign();
            $comptmo = $compaign->getTmo();
            $compaign->setTmo($comptmo++);
             /**@var Step */
            $step = $compaign->getSteps()->getValues()[$step];
        
            $steptmo = $step->getTmo();
            $step->setTmo($steptmo++);

            if($lead != null) 
            {
                $this->crud->saveLead($lead);
                $this->crud->saveCompaign($compaign);
                $this->crud->saveStep($step);

            }
        }
        return new TransparentPixelResponse();
        // return $this->render('tracking/index.html.twig', [
        //     'controller_name' => 'TrackingController',
        // ]);
    }

    #[Route('/image/{id}/{stepkey}/papillon.png',name:'app_tracking_2')]
    public function tracker(Lead $lead, int $stepkey)
    {
        if($lead == null) return null;
        //making change for lead
        // $leadName = $lead->getName();
        // $lead->setName($leadName."1");

        
        
        $compaign = $lead->getCompaign();
        $comptmo = $compaign->getTmo();
        $compaign->setTmo($comptmo++);
        /**@var Step */
        $step = $compaign->getSteps()->getValues()[$step];
        
        $steptmo = $step->getTmo();
        $step->setTmo($steptmo++);
        
        $this->crud->saveLead($lead);
        $this->crud->saveCompaign($compaign);
        $this->crud->saveStep($step);
        

        return $this->redirect("https://op.clemaos.com/Public/images/image.png");


    }
}
