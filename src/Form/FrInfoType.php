<?php

namespace App\Form;

use App\Entity\FrInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>"off"],
                'label' => "Message"
            ])
            ->add('debut', DateType::class,[
                'attr'=>['class'=>'form-control js-datepicker', 'autocomplete'=>"off"],
                'widget' => 'single_text',
                'html5' => false,
                'label' => "Début période"
            ])
            ->add('fin', DateType::class,[
                'attr'=>['class'=>'form-control js-datepicker', 'autocomplete'=>"off"],
                'widget' => 'single_text',
                'html5' => false,
                'label' => "Fin période"
            ])
            ->add('statut', CheckboxType::class,[
                'attr' => ['class'=>'form-check-input'],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FrInfo::class,
        ]);
    }
}
