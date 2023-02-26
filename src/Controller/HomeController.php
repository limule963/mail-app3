<?php

namespace App\Controller;

use App\Entity\Dsn;
use App\AppMailer\Data\STATUS;
use App\Entity\Lead;
use App\Entity\Step;
use App\Entity\Test;
use Twig\Environment;
use App\AppMailer\Data\EmailData;
use App\Entity\Schedule;
use App\AppMailer\Sender\Sequencer;
use App\AppMailer\Sender\EmailSender;
use App\AppMailer\Sender\SimpleObject;
use App\AppMailer\Sender\CompaignLuncher;
use App\AppMailer\Sender\SequenceLuncher;
use App\Repository\DsnRepository;
use Symfony\Component\Mime\Email;
use Twig\Loader\FilesystemLoader;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Config\TwigExtra\CssinlinerConfig;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    public function __construct(private CrudControllerHelpers $crud)
    {
        
    }
    #[Route('/', name: 'app_home')]
    public function index(CompaignLuncher $cl,CrudControllerHelpers $crud, Sequencer $seq,SequenceLuncher $sl): Response
    {


        $compaign = $crud->getCompaign();
        $compaign->setStatus($this->crud->getStatus(STATUS::COMPAIGN_ACTIVE));

        $cr = $cl->sequence($compaign)->lunch();

        // $sequence = $seq->sequence($compaign);
        // $cr = $sl->lunch($sequence);
        // dd($cr);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'cr'=>$cr
            
        ]);


    }
    #[Route('pp',name:'app_pp')]
    public function pp(CrudControllerHelpers $crud)
    {
        // $crud->addCompaign('koff');

        $compaign = $crud->getCompaign();
        $leads = $crud->getLeadBySender(7,'step.1','');
        dd($leads);
        return $this->render('home/index.html.twig',[
            'controller_name'=>'HomeController'
        ]);
    }

    #[Route('em',name:'app_em')]
    public function em(CrudControllerHelpers $crud)
    {

        return $this->render('mail/mail20.html.twig',[
        ]);
    }


    #[Route('/test',name:'app_test')]
    public function test(DsnRepository $rep,EmailSender $em,Environment $twig,BodyRendererInterface $bodyRenderer)
    {

        return $this->render('home/index.html.twig', [

        ]);
    }
}
