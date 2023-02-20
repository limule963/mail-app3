<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FormController extends AbstractController
{
    private $em;
    public function __construct(private ManagerRegistry $doctrine ,private UserPasswordHasherInterface $hasher)
    {
        $this->em = $doctrine->getManager();
    }
    #[Route('/register', name: 'app_form_register')]
    public function index(Request $request,): Response
    {
        // $user = new User;
        
        $form = $this->createForm(RegisterFormType::class);
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            /**
             * @var User
             */
            $user = $form->getData();
            // dd($user);
            
            
            $plainPassword = $user->getPassword();
            $hashedPassword = $this->hasher->hashPassword($user,$plainPassword);
            $user->setPassword($hashedPassword);
            
            $this->em->persist($user);
            $this->em->flush();


        }

        return $this->render('form/register.html.twig', [
            'form'=>$form
        ]);
    }
}
