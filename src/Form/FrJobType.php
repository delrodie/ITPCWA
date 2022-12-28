<?php

namespace App\Form;

use App\Entity\FrJob;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class FrJobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('reference')
            ->add('titre', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>"off"],
                'label' => "Titre"
            ])
            ->add('contrat', ChoiceType::class,[
                'attr' => ['class'=>'form-select', 'autocomplete'=>"off"],
                'choices' => [
                    '-- Selectionez  --' => '',
                    '' => '',
                    'BENEVOLAT' => 'BENEVOLAT',
                    'CDD' => 'CDD',
                    'CDI' => 'CDI',
                    'CONSULTANT' => 'CONSULTANT',
                    'STAGE' => 'STAGE',
                    'STAGE + EMBAUCHE' => 'STAGE + EMBAUCHE',
                ],
                'label' => "Type de contrat"
            ])
            ->add('contenu', CKEditorType::class,['attr'=>['class'=>'form-control']])
            ->add('media', FileType::class,[
                'attr'=>['class'=>"dropify-fr", 'data-preview' => ".preview"],
                'label' => "Télécharger la photo d'illustration",
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
                'label' => "Date fin de recrutement"
            ])
            ->add('lieu', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>"off"],
                'label' => "Lieu de prise de fonction"
            ])
            ->add('tags', TextType::class,[
                'attr'=>['class'=>'form-control', 'data-role'=>'tagsinput'],
                'label' => "Mots clés (séparez les mots par des virgules)"
            ])
            //->add('updatedAt')
            //->add('pageIndex')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FrJob::class,
        ]);
    }
}
