<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>"off", 'placeholder'=>"Name"],
                'constraints' => [
                    new NotBlank()
                ],
                'label' => "Name"
            ])
            ->add('email', EmailType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>'off', 'placeholder'=>"Email address"],
                'constraints' => [
                    new Email(),
                    new NotBlank()
                ],
                'label' => "Email address"
            ])
            ->add('localisation', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>"off", 'placeholder'=>"Location"],
                'constraints' => [
                    new NotBlank()
                ],
                'label' => "Location"
            ])
            ->add('message', TextareaType::class,[
                'attr'=>['class'=>'form-control', 'placeholder'=>"Message", 'style'=>"min-height:200px"],
                'constraints' => [
                    new NotBlank()
                ],
                'label' => "Message"
            ])
            //->add('statut')
            //->add('createdAt')
            //->add('updatedAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
