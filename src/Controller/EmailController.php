<?php
    namespace App\Controller;

use App\Entity\Dsn;
use App\Entity\Lead;
// use App\Entity\Email;
use App\AppMailer\Data\EmailData;
use Symfony\Component\Mime\Email;
use SecIT\ImapBundle\Service\Imap;
use Symfony\Component\Mailer\Mailer;
use App\AppMailer\Sender\EmailSender;
use App\Form\AddDsnType;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    class EmailController extends AbstractController
    {
        public function __construct(private CrudControllerHelpers $crud)
        {
            
        }
        
        
        #[Route('app/email',name:'app_email')]

        public function email()
        {



            return $this->render('Email/email.html.twig',[
            ]);
        }

        #[Route('app/email/add',name:'app_email_add')]

        public function addemail(Request $request)
        {
            $dsn = new Dsn;
            $form = $this->createForm(AddDsnType::class,$dsn);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {

                if (!$this->testDsnSmtp($dsn)) 
                {   
                    $this->addFlash("danger","smtp connexion failed");
                }
                else if (!$this->testDsnImap($dsn)) 
                {
                    $this->addFlash("danger","imap connexion failed");

                }
                
                else  
                {
                    $this->crud->addDsnToUser($dsn);
                    
                    $this->addFlash("success","Email Added");

                    return $this->redirectToRoute('app_email');
                }   
            }

            return $this->render('Email/add/email.html.twig',[
                'form'=>$form,
            ]);
        }

        

        #[Route('/app/email/delete/{id}',name:'app_email_delete')]
        public function deleteEmail(Dsn $dsn)
        {
            $this->crud->deleteDsn($dsn);
            $this->addFlash('success', 'Email deleted');
            return $this->redirectToRoute('app_email');
            
        }
        

        

        private function testDsnSmtp(Dsn $dsn)
        {   
            $transport = Transport::fromDsn($dsn->getDsn());
            $mailer = new Mailer($transport);
            $email = (new Email)->from($dsn->getEmail())->to($dsn->getEmail())->text('');

            try
            {
                 $mailer->send($email);
                 return true;
            }
            catch(\Throwable $th)
            {
                return false;
            }
           
        }
        private function testDsnImap(Dsn $dsn)
        {   
            $imap = new Imap($dsn->getConnexion());

            return $imap->testConnection($dsn->getConnexionName());       
        }


        
    } 

    