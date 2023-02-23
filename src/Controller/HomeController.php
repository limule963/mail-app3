<?php

namespace App\Controller;

use App\Classes\CompaignLuncher;
use App\Classes\EmailSender;
use App\Classes\SequenceLuncher;
use App\Classes\Sequencer;
use App\Classes\SimpleObject;
use App\Data\EmailData;
use App\Entity\Dsn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    public function __construct(private CrudControllerHelpers $crud)
    {
        
    }
    #[Route('/', name: 'app_home')]
    public function index(CompaignLuncher $sender): Response
    {

        try 
        {
            $user = $this->crud->user();
         
             $error =null;
        } 
        catch (\Throwable $th) 
        {
            $error = $th->getMessage();
            return $this->redirectToRoute('app_login');
        }
        

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'error'           => $error
        ]);


    }


    #[Route('/test',name:'app_test')]
    public function test(EmailSender $em)
    {

        $Dsn = (new Dsn)->setEmail('contact@clemaos.com')->setUsername('contact@clemaos.com')
                        ->setPassword('jC1*rAJ8GGph9@u')->setHost('mail51.lwspanel.com')->setPort(587);
        $dsn = $Dsn->getDsn();
        // dd($dsn);

        $email = (new Email())->subject('Hello')->html('<div> Lorem ipsum dolor</div>')->text('Lorem ipsum dolor');
        // dd($email);
        $from =$Dsn->getEmail();

        $to = 'clemaos@yahoo.fr';
        $emailData = new EmailData($dsn,$from,$to,$email);

        // dd($emailData);

        $emailResponse = $em->send($emailData);



        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'code'=>$emailResponse->code,
            'message'=>$emailResponse->message. '<br>'.$emailResponse->throwMessage
        ]);
    }
}
