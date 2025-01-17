<?php

namespace App\Controller;

use App\Entity\Dsn;
use App\Entity\User;
use Twig\Environment;
use App\AppMailer\Data\MailData;
use App\AppMailer\Data\Connexion;
use App\AppMailer\Data\STATUS;
use App\Repository\DsnRepository;
use SecIT\ImapBundle\Service\Imap;
use App\AppMailer\Sender\Sequencer;
use Symfony\Component\Mime\Address;
use App\AppMailer\Receiver\ImapCurl;
use App\AppMailer\Receiver\Receiver;
use App\AppMailer\Receiver\MailSaver;
use App\AppMailer\Sender\EmailSender;
use Symfony\Component\Mailer\Transport;
use App\AppMailer\Sender\CompaignLuncher;
use App\AppMailer\Sender\SequenceLuncher;
use App\AppMailer\Receiver\AllDsnReceiver;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\AppMailer\Receiver\AllFolderReceiver;

use App\AppMailer\Receiver\CompaignMailSaver;
use App\Repository\LeadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Uid\Uuid;
use Throwable;

class HomeController extends AbstractController
{

    public function __construct(private CrudControllerHelpers $crud)
    {
        
    }
    #[Route('/', name: 'app_home')]
    public function index( Request $request,Receiver $rec,Connexion $con,LeadRepository $rep): Response
    {



 


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'cr'=>''
        ]);


    }


    #[Route('/pp',name:'app_pp')]
    public function pp(CompaignLuncher $cl)
    {
        /**@var User $user */
        // $user = $this->getUser();
        // $userId = $user->getId();
        
        $compaign = $this->crud->getCompaign(1,6);

        
        // dd($compaign);
        // $compaign->setStatus($this->crud->getStatus(STATUS::COMPAIGN_ACTIVE));
        $cr = $cl->sequence($compaign)->lunch();




        return $this->render('home/index.html.twig',[
            'controller_name'=>'HomeController',
            'cr'=>$cr
        ]);
    }

    #[Route('em',name:'app_em')]
    public function em(CrudControllerHelpers $crud,BodyRendererInterface $bd)
    {


    
        return $this->render('mail17.html.twig',[
        ]);
    }


    #[Route('/test',name:'app_test')]
    public function test(DsnRepository $rep,EmailSender $em,Environment $twig,BodyRendererInterface $bodyRenderer)
    {

        return $this->render('home/index.html.twig', [

        ]);
    }

    #[Route('/imap',name:'app_imap')]
    public function imap(Receiver $rec)
    {
        $dsn = $this->crud->getUserDsn(1,4);
        $imap = new Imap($dsn->getConnexion());
        $test = $imap->testConnection($dsn->getConnexionName());
        dd($dsn,$imap,$test);
        

        return $this->render('home/index.html.twig',[
            "controller_name"=>'HomeController',
            'cr'=>''
        ]);

    }

    #[Route('/ms',name:'app_ms')]
    public function ms(CompaignMailSaver $ms)
    {
        $compaign = $this->crud->getCompaign(1,6);
        $res = $ms->save($compaign);
        return new  Response('done');
    }
    



}
