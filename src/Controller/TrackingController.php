<?php

namespace App\Controller;

use App\Entity\Mo;
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
        $stepId = $request->query->get('stepId');
        // dd($id);
        if (null !== $id) {
            //... executes some logic to retrieve the email and mark it as opened

            $lead = $this->crud->getLead($id);
            //code for lead
            $step = $this->crud->getStep($stepId);
            
            $compaign = $lead->getCompaign();
            $mo = (new Mo)->setSender($lead->getSender())->setMoLead($lead)->setStep($step)->setCompaign($compaign);
            $this->crud->saveMo($mo,false);

            $comptmo = $compaign->getTmo();
            $compaign->setTmo($comptmo++);
            
            $steptmo = $step->getTmo();
            $step->setTmo($steptmo++);


            $this->crud->saveLead($lead,false);
            $this->crud->saveCompaign($compaign,false);
            $this->crud->saveStep($step,false);

            $this->crud->em->flush();

            
        }
        return new TransparentPixelResponse();
        // return $this->render('tracking/index.html.twig', [
        //     'controller_name' => 'TrackingController',
        // ]);
    }

    #[Route('/image/{id}/{stepId}/papillon.png',name:'app_tracking_2')]
    public function tracker(Lead $lead, int $stepId)
    {
        if($lead == null) return null;
        //making change for lead
        // $leadName = $lead->getName();
        // $lead->setName($leadName."1");
        $step = $this->crud->getStep($stepId);
        if($step == null) return null;
        
        $compaign = $lead->getCompaign();
        
        $steptmo = $step->getTmo();
        

        $mo = (new Mo)->setSender($lead->getSender())->setMoLead($lead)->setStep($step)->setCompaign($compaign);
        $this->crud->saveMo($mo,false);

        $step->setTmo($steptmo++);


        // $compaign = $step->getCompaign();
        
        $comptmo = $compaign->getTmo();
        $compaign->setTmo($comptmo++);
        
        $this->crud->saveLead($lead,false);
        $this->crud->saveCompaign($compaign,false);
        $this->crud->saveStep($step,false);

        $this->crud->em->flush();


        return $this->redirect("https://op.clemaos.com/Public/images/image.png");


    }

    #[Route()]
    public function linkTracking()
    {
        
    }
    
}
