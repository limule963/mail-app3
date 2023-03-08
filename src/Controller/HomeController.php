<?php

namespace App\Controller;

use App\Entity\Dsn;
use App\Entity\Lead;
use App\Entity\User;
use Twig\Environment;
use App\AppMailer\Data\FOLDER;
use App\AppMailer\Data\STATUS;
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
use Symfony\Component\Serializer\Serializer;
use App\AppMailer\Receiver\AllFolderReceiver;

use App\AppMailer\Receiver\CompaignMailSaver;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Mailer;
use Throwable;

class HomeController extends AbstractController
{

    public function __construct(private CrudControllerHelpers $crud)
    {
        
    }
    #[Route('/', name: 'app_home')]
    public function index( AllDsnReceiver $alldsnRec, CompaignLuncher $cl, Sequencer $seq,SequenceLuncher $sl,AllFolderReceiver $allrec,Receiver $rec): Response
    {
        
        if($this->getUser() == null) return $this->redirectToRoute('app_login') ;
        $user = $this->getUser();
        /**@var User $user*/
        $userId = $user->getId();


        $leads = [
            [
                'name'=>'mawuelom',
                'email'=>'kofazia@gmail.com'
            ],
            [
                'name'=>'kokou',
                'email'=>'kokou@gmail.com'
            ],
            [
                'name'=>'koffi',
                'email'=>'koffi@gmail.com'
            ],
            [
                'name'=>'kodzo',
                'email'=>'kodzo@gmail.com'
            ],
        ];

        // $this->crud->addLeadsByfile(7,'leads.json','json');
        // $encoders = [new CsvEncoder(),new JsonEncoder() ];
        // $normalizers = [new ObjectNormalizer()];
        // $serializer = new Serializer($normalizers,$encoders);

        // $lead =(new Lead)->setName('azialoame')->setEmailAddress('kofazia@gmail.com');

        // $leadCsv = $serializer->serialize($lead,'csv');
        // dd($leadCsv);
        // $cr = $leadCsv;

        // $compaign = $this->crud->getCompaign($userId,7);

        // $compaign->setStatus($this->crud->getStatus(STATUS::COMPAIGN_ACTIVE));
        // $cr = $cl->sequence($compaign)->lunch();

        // $encoder = new CsvEncoder();
        // $encoder2 = new JsonEncoder();
        // $datajson = $encoder2->encode($leads,'json');
        // file_put_contents('leads.json',$datajson);
        // dd($datajson);
        // $data = file_get_contents('leads.csv','csv');
        // // $data2 = $serializer->deserialize($data,Lead::class,'csv') ;
        // $data2 = $encoder->decode($data,'csv');
        // // $data2 = $encoder->encode($data,'csv');
        // $normalizer = new ObjectNormalizer();

        // foreach($data2 as $data) $leads[] = $normalizer->denormalize($data,Lead::class);

        // dd($data2,$leads);

        // $fichier = 'leads.csv';




    

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            // 'cr'=>$cr
            
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
