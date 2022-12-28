<?php

namespace App\Form;

use App\Entity\EnJob;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EnJobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('reference')
            ->add('titre', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>"off"],
                'label' => "Title"
            ])
            //->add('resume')
            ->add('contenu', CKEditorType::class,[
                'attr'=>['class'=>'form-control'],
                'label' => "Content"
            ])
            ->add('media', FileType::class,[
                'attr'=>['class'=>"dropify-fr", 'data-preview' => ".preview"],
                'label' => "Download illustrative photo",
                'mapped' => false,
                'multiple' => false,
                'constraints' => [
                    new File([
                        'maxSize' => "20000m",
                        'mimeTypes' =>[
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                            'image/webp',
                        ]
                    ])
                ],
                'required' => false
            ])
            ->add('fin', DateType::class,[
                'attr'=>['class'=>'form-control js-datepicker', 'autocomplete'=>"off"],
                'widget' => 'single_text',
                'html5' => false,
                'label' => "Recruitment end date"
            ])
            ->add('lieu', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>"off"],
                'label' => "Place of taking office"
            ])
            //->add('slug')
            ->add('contrat', ChoiceType::class,[
                'attr' => ['class'=>'form-select', 'autocomplete'=>"off"],
                'choices' => [
                    '-- Select  --' => '',
                    '' => '',
                    'VOLUNTEERING' => 'VOLUNTEERING',
                    'FIXED-TERM CONTRACT' => 'FIXED-TERM CONTRACT',
                    'PERMANENT CONTRACT' => 'PERMANENT CONTRACT',
                    'CONSULTANT' => 'CONSULTANT',
                    'INTERNSHIP' => 'INTERNSHIP',
                    'INTERNSHIP + EMPLOYMENT' => 'INTERNSHIP + EMPLOYMENT',
                ],
                'label' => "Contract type"
            ])
            ->add('tags', TextType::class,[
                'attr'=>['class'=>'form-control', 'data-role'=>'tagsinput'],
                'label' => "Keywords (separate words with commas)"
            ])
            //->add('createdAt')
            //->add('updatedAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EnJob::class,
        ]);
    }
}
