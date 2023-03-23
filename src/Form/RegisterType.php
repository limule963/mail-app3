<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('firstname',TextType::class,[
            //     'label'=>'Votre prenom',
            //     'attr'=>[
            //         'placeholder' =>'Saisissez votre prenom'
            //     ]
            // ])
            // ->add('lastname',TextType::class,[
            //     'label'=>'Votre nom',
            //     // 'constraints'=>new Length(min:5,max:8)
            // ])
            ->add('email',EmailType::class,[
                'required'=>true
            ])

            ->add('password',RepeatedType::class,[
                'type' =>PasswordType::class,
                'invalid_message'=>'password not the same',
                'required'=>true,
                'first_options'=>['label'=>'your password'],
                'second_options'=>['label'=> 'confirm your password']
            ])

            ->add('submit',SubmitType::class,[
                'label'=>'Register'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
