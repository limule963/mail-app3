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

class AddDsnType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,['label'=>'Sender Name'])
            ->add('email',EmailType::class,['label'=>'Email'])
            ->add('username',TextType::class,['label'=>'Smtp username'])
            ->add('password',PasswordType::class,['label'=>'Smtp password'])
            ->add('host',TextType::class,['label'=>'Smtp host'])
            ->add('port',NumberType::class,['label'=>'smtp port'])

            ->add('username2',TextType::class,['label'=>'Imap username'])
            ->add('password2',PasswordType::class,['label'=>'Imap password'])
            ->add('host2',TextType::class,['label'=>'Imap host'])
            ->add('port2',NumberType::class,['label'=>'Imap port'])
            ->add('submit',SubmitType::class,['label'=>'Add Email'])
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
