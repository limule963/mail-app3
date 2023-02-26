<?php

namespace App\Controller;

use App\Entity\Dsn;
use App\Data\STATUS;
use App\Entity\Lead;
use App\Entity\Step;
use App\Entity\Test;
use Twig\Environment;
use App\Data\EmailData;
use App\Entity\Schedule;
use App\Classes\Sequencer;
use App\Classes\EmailSender;
use App\Classes\SimpleObject;
use App\Classes\CompaignLuncher;
use App\Classes\SequenceLuncher;
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


    #[Route('/test',name:'app_test')]
    public function test(DsnRepository $rep,EmailSender $em,Environment $twig,BodyRendererInterface $bodyRenderer)
    {

        $Dsn = (new Dsn)->setEmail('contact@clemaos.com')->setUsername('contact@clemaos.com')
                        ->setPassword('jC1*rAJ8GGph9@u')->setHost('mail51.lwspanel.com')->setPort(587);
        $dsn2 = $Dsn->getDsn();
        $Dsn = $rep->find(9);
        // dd($Dsn);
        $dsn = $Dsn->getDsn();
        dd($dsn,$dsn2);
        
        // dd($dsn);
        
        $lead = (new Lead)->setName('clemaos')->setEmailAddress('clemaos@yahoo.fr');
        $email = (new TemplatedEmail())
                ->subject('Hello')
                ->htmlTemplate('mail/mail17.html.twig')
                
                ->context([
                    'lead'=>$lead

                ])

        ;
        $bodyRenderer->render($email);
        // dd($email);


 




        $from =$Dsn->getEmail();

        $to = 'clemaos@yahoo.fr';
        $emailData = new EmailData($dsn,$from,$to,$email,'');

        // dd($emailData);

        $emailResponse = $em->send($emailData);



        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'code'=>$emailResponse->code,
            'message'=>$emailResponse->message. ' '.$emailResponse->throwMessage
        ]);
    }
}
