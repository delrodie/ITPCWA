<?php

namespace App\Form;

use App\Entity\EnEquipe;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EnEquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>"off"],
                'label' => "Name "
            ])
            ->add('prenom', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>"off"],
                'label' => "First name "
            ])
            ->add('fonction', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>"off"],
                'label' => "Function"
            ])
            ->add('contenu', CKEditorType::class,[
                'attr'=>['class'=>'form-control'],
                'label' => "Biography"
            ])
            ->add('media', FileType::class,[
                'attr'=>['class'=>"dropify", 'data-preview' => ".preview"],
                'label' => "Download picture",
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
            ->add('statut', CheckboxType::class,[
                'attr' => ['class'=>'form-check-input'],
                'required' => false,
                'label' => "status"
            ])
            //->add('createdAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EnEquipe::class,
        ]);
    }
}
