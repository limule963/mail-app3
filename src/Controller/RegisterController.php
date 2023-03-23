<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $entityManager;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine,)
    {
        $this->entityManager = $doctrine->getManager();
        $this->doctrine = $doctrine;
 
    }
    #[Route('/register', name: 'app_register')]
    public function index(Request $request,UserPasswordHasherInterface $passwordHasher): Response
    {

        $user = new User;
        
        $form = $this->createForm(RegisterType::class,$user);
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            
            $user = $form->getData();
            
            
            $plainPassword = $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword($user,$plainPassword);
            $user->setPassword($hashedPassword);
            
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_email');

        }

        return $this->render('register/index.html.twig', [
            'form'=>$form
        ]);
    }
}
