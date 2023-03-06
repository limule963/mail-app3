<?php

namespace App\Form;

use App\Entity\Dsn;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddDsnImapType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('name',TextType::class,['label'=>'Name'])
            // ->add('email',EmailType::class,['label'=>'Email'])
            // ->add('username',TextType::class,['label'=>'Username'])
            // ->add('password',PasswordType::class,['label'=>'Password'])
            // ->add('host',TextType::class,['label'=>'Host'])
            // ->add('port',NumberType::class,['label'=>'Port'])
            ->add('username2',TextType::class,['label'=>'Username'])
            ->add('password2',PasswordType::class,['label'=>'Password'])
            ->add('host2',TextType::class,['label'=>'Host'])
            ->add('port2',NumberType::class,['label'=>'Port'])
            ->add('submit',SubmitType::class,['label'=>'Add'])
            // ->add('connexionName')
            // ->add('createAt')
            // ->add('compaign')
            // ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dsn::class,
        ]);
    }
}
