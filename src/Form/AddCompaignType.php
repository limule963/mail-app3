<?php

namespace App\Form;

use App\Entity\Compaign;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddCompaignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,['label'=>'Compaign Name'])
            ->add('submit',SubmitType::class,['label'=>'Add'])

            // ->add('newStepPriority')
            // ->add('createAt')
            // ->add('user')
            // ->add('status')
            // ->add('schedule')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Compaign::class,
        ]);
    }
}
