<?php

namespace App\Controller;



use App\Entity\User;

use App\AppMailer\Receiver\CompaignMailSaver;

use App\Entity\Compaign;

use App\AppMailer\Data\STATUS;
use App\AppMailer\Receiver\BulkCompaignMailSaver;
use App\AppMailer\Sender\BulkCompaignLuncher;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;



class CompaignLuncherController extends AbstractController

{

    public function __construct(
        private BulkCompaignLuncher $bcl,
        private CompaignMailSaver $cms,
        private BulkCompaignMailSaver $bcms)

    {

        

    }

    #[Route('/compaign/user/{id}/lunch',name:"app_compaign_cron_lunch")]

    public function lunchCompaign(User $user)

    {



        if($user == null)  return new Response('done');

        /**@var Compaign[] */
        $compaigns = $user->getCompaigns()->getValues();
        // dd($compaigns);
        $this->bcms->saveall($compaigns);
        

        $this->bcl->lunch($compaigns);
        return new Response('done');



    }

    

    





    

}