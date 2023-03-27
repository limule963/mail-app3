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



    #[Route('{leadId}/{stepId}/image.gif', name: 'app_tracking')]

    public function index(int $leadId,int $stepId): Response

    {

        

        // $id = $request->query->get('id');

        // $stepId = $request->query->get('stepId');

        

        // $id = intval($id);

        // $stepId = intval($stepId);

 

        $lead = $this->crud->getLead($leadId);

        $step = $this->crud->getStep($stepId);


        $compaign = $lead->getCompaign();

        $dsn = $lead->getDsn();

        

        $mo = $this->crud->getMoByStepAndLead($compaign->getId(),$stepId,$leadId);

        if($mo == null) 

        {

            $mo = (new Mo)->setDsn($dsn)->setSender($lead->getSender())->setMoLead($lead)->setStep($step)->setCompaign($compaign);

            $this->crud->saveMo($mo,true);

        }



        return new TransparentPixelResponse();

        // return $this->render('tracking/index.html.twig', [

        //     'controller_name' => 'TrackingController',

        // ]);

    }



    #[Route('/image/{id}/{stepId}/papillon.png',name:'app_tracking_2')]

    public function tracker(Lead $lead, int $stepId)

    {

        

        $step = $this->crud->getStep($stepId);



        if($lead == null) return null;

        if($step == null) return null;

        

        $compaign = $lead->getCompaign();

        $dsn = $lead->getDsn();

        

        $mo = $this->crud->getMoByStepAndLead($compaign->getId(),$step->getId(),$lead->getId());

        if($mo == null)

        {

            $mo = (new Mo)->setDsn($dsn)->setSender($lead->getSender())->setMoLead($lead)->setStep($step)->setCompaign($compaign);

            $this->crud->saveMo($mo,true);



        }





        return $this->redirect("https://app.clemaos.com/assets/images/transparent.png");





    }



    #[Route()]

    public function linkTracking()

    {

        

    }

    

}

