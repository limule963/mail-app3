<?php

namespace App\Controller;

use App\Entity\Dsn;
use App\Entity\User;
use Twig\Environment;
use App\AppMailer\Data\MailData;
use App\AppMailer\Data\Connexion;
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
    public function index( Request $request): Response
    {

        $data = $request->files->all();
        // dd($data);

        $form = $this->createFormBuilder(null,['csrf_protection'=>true])
                        ->add('name',TextType::class)
                        ->add('text',CKEditorType::class,["attr"=>['rows' => 20]])
                        ->add('submit',SubmitType::class)
                        ->getForm()
                        ;


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form'=>$data           
        ]);


    }
    #[Route('pp',name:'app_pp')]
    public function pp(ImapCurl $ic, MailData $md, MailSaver $ms ,CompaignMailSaver $cms,CrudControllerHelpers $crud,AllDsnReceiver $alldsnRec,Receiver $rec,Connexion $connect,AllFolderReceiver $allrec)
    {
        /**@var User $user */
        $user = $this->getUser();
        $userId = $user->getId();
        $compaign = $this->crud->getCompaign($user->getId(),7);
        $dsns = $compaign->getDsns()->getValues();
        $dsn  = $dsns[2];

        $this->crud->deleteDsn($dsn);
        // dd($dsn);
        // $lead = $this->crud->getLeadByEmailAddress(7,'clemaos@yahoo.fr');
        // $response = $ic->send('All');
        // $mails =$allrec->receive($dsn,'UID 22');
        // dd($response);
        // $cms->save($dsns,7,1);

        // $ms->saveMails($md->set($dsn,7,1));


        // dd($compaign,$dsns,$lead,$mails);
        // $dsns = $user->getDsns()->getValues();

        /**@var Dsn */
        // $dsn = $dsns[2];
        // $date = new DateTimeImmutable();
        // $connect->set($dsn,'SUBJECT Re  FROM clemaos@yahoo.fr SINCE 2023-03-01');
        // dd($dsn,$connect);


        // $imap = new Imap($dsn->getConnexion());
        // $rec2 = $imap->get($dsn->getConnexionName());

        // $mailIds = $rec2->switchMailbox('inbox')->sortMails(searchCriteria:'all');
        // $infos = $rec2->getMailsInfo($mailIds);
        // dd($mailIds,$infos);

        
        // $mails = $rec->getMail($connect);
        // dd($mails);
        // $mails = $alldsnRec->getMails($dsns,1);
        // dd($mails);






        return $this->render('home/index.html.twig',[
            'controller_name'=>'HomeController',
            // 'cr'=>$mails
        ]);
    }

    #[Route('em',name:'app_em')]
    public function em(CrudControllerHelpers $crud,BodyRendererInterface $bd)
    {
        $lead = $this->crud->getLead(3);
        $dsns= $this->crud->getCompaignDsns(7);
        $dsn = $dsns[0];

        $transport = Transport::fromDsn($dsn->getDsn());

        $mailer = new Mailer($transport);

        


        $email = (new TemplatedEmail())->htmlTemplate('mail17.html.twig')
                                        ->to(new Address($lead->getEmailAddress(),$lead->getName()))
                                        ->subject('hello')
                                        ->from(new Address($dsn->getEmail(),$dsn->getName()))
                                        ->context([
                                            'lead'=>$lead,
                                            'tracker'=>true
                                        ])
                    ;

        $bd->render($email);
        // dd($email);
        try {
            $mailer->send($email);
            dd('done');
        }
        catch(Throwable $th){
            
             dd('not done');  
         } 








        return $this->render('mail17.html.twig',[
            'lead'=>$lead
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
