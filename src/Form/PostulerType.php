<?php

namespace App\Form;

use App\Entity\Candidat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class PostulerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('matricule')
            ->add('nom', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>'off', 'placeholder'=>"Enter your surname"],
                'label' => "Surname",
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('prenoms', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>'off', 'placeholder'=>"Enter your first name"],
                'label' => "First name",
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('phone', TelType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>"off", 'placeholder'=>"Enter your phone number"],
                'label' => "Phone",
                'constraints' =>[
                    new NotBlank()
                ]
            ])
            ->add('email', EmailType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>"off", 'placeholder'=>"Enter your email address"],
                'label' => "Email",
                'constraints' => [
                    new Email(),
                    new NotBlank()
                ]
            ])
            ->add('mediaLettre', FileType::class,[
                'attr'=>['class'=>"form-control"],
                'label' => "Upload your cover letter",
                'mapped' => false,
                'multiple' => false,
                'constraints' => [
                    new File([
                        'maxSize' => "20000000m",
                        'mimeTypes' =>[
                            'application/pdf',
                        ]
                    ]),
                    new NotBlank()
                ],
                'required' => true,
                'help' => "Only valid PDF document"
            ])
            ->add('mediaCV', FileType::class,[
                'attr'=>['class'=>"form-control"],
                'label' => "Upload your CV",
                'mapped' => false,
                'multiple' => false,
                'constraints' => [
                    new File([
                        'maxSize' => "20000000m",
                        'mimeTypes' =>[
                            'application/pdf',
                        ]
                    ]),
                    new NotBlank()
                ],
                'required' => true,
                'help' => "Only valid PDF document"
            ])
            //->add('jobReference')
            //->add('recaptcha')
            //->add('createdAt')
            //->add('updatedAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidat::class,
        ]);
    }
}
