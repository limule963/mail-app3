<?php

namespace App\Controller;

use App\AppMailer\Data\Connexion;
use App\Entity\Dsn;
use App\Entity\Lead;
use App\Entity\Step;
use App\Entity\Test;
use App\Entity\User;
use Twig\Environment;
use App\Entity\Schedule;
use App\AppMailer\Data\STATUS;
use App\AppMailer\Data\EmailData;
use App\AppMailer\Data\FOLDER;
use App\AppMailer\Data\Mail;
use App\Repository\DsnRepository;
use Symfony\Component\Mime\Email;
use Twig\Loader\FilesystemLoader;
use SecIT\ImapBundle\Service\Imap;
use App\AppMailer\Sender\Sequencer;
use App\AppMailer\Receiver\Receiver;
use App\AppMailer\Sender\EmailSender;
use App\AppMailer\Sender\SimpleObject;
use App\AppMailer\Sender\CompaignLuncher;
use App\AppMailer\Sender\SequenceLuncher;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\AppMailer\Receiver\AllFolderReceiver;
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
    public function index(CompaignLuncher $cl, Sequencer $seq,SequenceLuncher $sl,AllFolderReceiver $allrec,Receiver $rec): Response
    {
        
        if($this->getUser() == null) return $this->redirectToRoute('app_login') ;
        $user = $this->getUser();
        /**@var User $user*/
        $userId = $user->getId();

        
        $dsn = $this->crud->getUserDsn($userId,9);
        
        // $receiver = new Receiver(new Mail);
        // $receiver2 = new Receiver(new Mail);

        // $mails1 =$receiver->getMail((new Connexion)->set($dsn,FOLDER::INBOX));
        // $mails2 =$receiver2->getMail((new Connexion)->set($dsn,FOLDER::SENT));

        // $mails1=  $rec->getMail((new Connexion)->set($dsn,FOLDER::INBOX));
        $mails2=  $rec->getMail((new Connexion)->set($dsn,'from clemaos@yahoo.fr',FOLDER::INBOX));
        dd($mails2);
        // dd($mails1,$mails2);

        $mails  = $allrec->receive($dsn);

        dd($mails);


        
        $imap = new Imap($dsn->getConnexion());
        
        $test = $imap->testConnection($dsn->getConnexionName());
        
        
        
        $con = $imap->get($dsn->getConnexionName());
        $folders = $con->getListingFolders();
        dd($folders);
        // $con->renameMailbox('koff','koffa');
        // $con->subscribeMailbox('koffa');
        // $con->switchMailbox('Sent');
        $mailIds =$con->searchMailbox();
        // $mailIds3 = $con->switchMailbox('Drafts')->searchMailbox();
        // $infos3 = $con->getMailsInfo($mailIds3);
        // dd('done',$mailIds3,$infos3);
        $mailIds2 = $con->sortMails();
        $infos = $con->getMailsInfo($mailIds);
        $infos2 = $con->getMailsInfo($mailIds2);
        $mail = $con->getMail($mailIds2[0],false)->subject;
        
        $con->disconnect();
        dd($mailIds,$mailIds2,$infos,$infos2,$mail);

        // dd($test,$folders,$mailIds,$ids,$mail);


        $stamp = $dsn->getCreateAt()->getTimestamp();
        $date =getdate($stamp);
        $criteria = 'since '.$date['year'].'-'.$date['mon'].'-'.$date['mday'];
        // dd($criteria);
        
        $mid =$con->searchMailbox(criteria:$criteria);
        
        dd($mid,$con->getMail(1,false),$con->getMail(8,false)->date);
        
        
        
        
        
        
        

        $compaign = $this->crud->getCompaign($userId,9);

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

    #[Route('/imap',name:'app_imap')]
    public function imap()
    {
        $connexions = [
            'clemaos'=>[
                
                'mailbox'=>"{mail51.lwspanel.com:993/imap/ssl}INBOX",
                'username'=>'contact@clemaos.com',
                'password'=>'jC1*rAJ8GGph9@u',
                'attachments_dir'=> "%kernel.project_dir%/var/imap/attachments",
                'server_encoding'=> "UTF-8",
                'create_attachments_dir_if_not_exists'=>true, // default true
                'created_attachments_dir_permissions'=> 777  // default 770
            ],

            'aykode'=>[
                'mailbox'=>"{mail56.lwspanel.com:993/imap/ssl}INBOX",
                'username'=>'contact@aykode.com',
                'password'=>'dF4_pxe9t!5q_wa',
                'attachments_dir'=> "%kernel.project_dir%/var/imap/attachments",
                'server_encoding'=> "UTF-8",
                'create_attachments_dir_if_not_exists'=>true, // default true
                'created_attachments_dir_permissions'=> 777  // default 770

            ],
            
            'crubeo'=>[
                'mailbox'=>"{mail52.lwspanel.com:993/imap/ssl}INBOX",
                'username'=>'contact@crubeo.fr',
                'password'=>'rS1*aahSMZhKq9q',
                'attachments_dir'=> "%kernel.project_dir%/var/imap/attachments",
                'server_encoding'=> "UTF-8",
                'create_attachments_dir_if_not_exists'=>true, // default true
                'created_attachments_dir_permissions'=> 777  // default 770
            ]
        ];

        $imap = new Imap($connexions);
        $con = $imap->get('clemaos');
        // $isConnectable = $imap->testConnection('clemaos');
        // $mailboxInfos = $con->getMailboxInfo();
        $con->setImapSearchOption(SE_FREE);
        $mailIds = $con->searchMailbox('SINCE 2023-02-26');
        // $folders = $con->getMailboxes();
        // $con->switchMailbox('koff');
        // $con->renameMailbox('koff','Azia');
        // $con->createMailbox('Azia');
        // $lf= $con->getMailboxes();
        // $mail = $con->getMail(8,false);
        
        $con->disconnect();
        dd($mailIds);
        

        return $this->render('home/index.html.twig',[
            "controller_name"=>'HomeController',
            'cr'=>''
        ]);























    }
}
