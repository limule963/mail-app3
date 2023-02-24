<?php

namespace App\Controller;

use App\Entity\Dsn;
use App\Entity\Step;
use App\Data\EmailData;
use App\Classes\Sequencer;
use App\Classes\EmailSender;
use App\Classes\SimpleObject;
use App\Classes\CompaignLuncher;
use App\Classes\SequenceLuncher;
use App\Entity\Schedule;
use App\Entity\Test;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    public function __construct(private CrudControllerHelpers $crud)
    {
        
    }
    #[Route('/', name: 'app_home')]
    public function index(CrudControllerHelpers $crud): Response
    {
        // $crud->addCompaign('compagne1');
        // $compaign = $crud->getCompaign();
        // dd($compaign);

        dd(getdate()['hours']);

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
