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
    public function index(DsnRepository $rep,CrudControllerHelpers $crud, Sequencer $seq,SequenceLuncher $sl): Response
    {

        // $dsnDatas = [
        //     [
        //         'email'=> 'contact@aykode.com',
        //         'username' => 'contact@aykode.com', 
        //         'password'=> 'dF4_pxe9t!5q_wa',
        //         'host'=> 'mail56.lwspanel.com',
        //         'port'=>587 
        //     ],
        //     [
        //         'email'=> 'contact@crubeo.fr',
        //         'username' => 'contact@crubeo.fr', 
        //         'password'=> 'rS1*aahSMZhKq9q',
        //         'host'=> 'mail52.lwspanel.com',
        //         'port'=>587 
        //     ],
        //     [
        //         'email'=> 'contact@clemaos.com',
        //         'username' => 'contact@clemaos.com', 
        //         'password'=> 'jC1*rAJ8GGph9@u',
        //         'host'=> 'mail51.lwspanel.com',
        //         'port'=>587 
        //     ]
        // ];
        // foreach($dsnDatas as $dsnData) $crud->addDsnToUser($dsnData);
        // dd('done');

        
        // $dsns = $crud->getUserDsns();
        
        // foreach($dsns as $dsn) $crud->addDsnToCompaign($compaign->getId(),$dsn);
        
        // dd($dsns);
        
        
        // dd($compaign);
        // $dsns = $crud->getCompaignDsns($compaign->getId());
        // dd($dsns);
        // // foreach($dsnDatas as $dsnData) $crud->addDsnToCompaign($compaign->getId(),$dsnData);
        // dd('done');
        // // $crud->addCompaign('compagne1');
        // $dsns = $crud->getCompaignDsns($compaign->getId());
        // dd($dsns);
        // $dsn = $rep->find(9);
        // dd($dsn);
        
        $compaign = $crud->getCompaign();

        $sequence = $seq->sequence($compaign);
        $cr = $sl->lunch($sequence);
        dd($cr);


        // $crud->addLeads($compaign->getId(),$crud->createLead('alice','alice.brunett44@gmail.com'));
        // $crud->addLeads($compaign->getId(),$crud->createLead('koff','kofazia@gmail.com'));
        // $crud->addLeads($compaign->getId(),$crud->createLead('clemaos','clemaos@yahoo.fr'));
        // $crud->addLeads($compaign->getId(),$crud->createLead('koffi azialoame','koffi.azialoame@yahoo.com'));


    
        // $crud->addStep($compaign->getId(),'informer','bonjour','mail17.html.twig');
        // $crud->addStep($compaign->getId(),'proceder','bonjour','mail16.html.twig');
        dd($compaign);

        // dd(getdate()['hours']);

        // $crud->deleteCompaign($compaign);

        // $steps = $compaign->getSteps()->getValues();

     
        // $step = $steps[0];
        // $status = $step->getStatus();
        // $sch = (new Schedule)->setStartTime(new \DateTimeImmutable())->setFrom(8)->setTo(18);

        // dd($sch);
        // $crud->updateCompaign($compaign->getId(),schedule:$sch);
        // $crud->deleteCompaign($compaign->getId());
        // dd($compaign,$steps,$status);
        // $crud->addStep($compaign->getId(),'premier contact','salutation','c://KOFF/mail.html.twig');
        // $crud->addStep($compaign->getId(),'trie','helo','c://KOFF/mail2.html.twig');
        // $crud->addStep($compaign->getId(),'finalisation','last chance','c://KOFF/mail3.html.twig');
        // $crud->addCompaign('compagne3');
        // $crud->saveSchedule($schedule);    



        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            
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
