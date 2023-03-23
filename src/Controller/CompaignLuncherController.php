<?php
namespace App\Controller;

use App\Entity\User;
use App\AppMailer\Receiver\CompaignMailSaver;
use App\Entity\Compaign;
use App\AppMailer\Data\STATUS;
use App\AppMailer\Sender\BulkCompaignLuncher;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompaignLuncherController extends AbstractController
{
    public function __construct(private BulkCompaignLuncher $bcl,private CompaignMailSaver $cms)
    {
        
    }
    #[Route('/compaign/lunch/cron/user/{id}',name:"app_compaign_cron_lunch")]
    public function lunchCompaign(User $user)
    {
        /**@var Compaign[] */
        $compaigns = $user->getCompaigns()->getValues();

        foreach($compaigns as $compaign)
        {
            $status = $compaign->getStatus()->getStatus();
            
            if($status == STATUS::COMPAIGN_COMPLETE || $status == STATUS::COMPAIGN_PAUSED)
            {
                $this->cms->save($compaign);
            }
            if($status != STATUS::COMPAIGN_ACTIVE) continue;
        }

        $this->bcl->lunch($compaigns);

    }
    
    


    
}