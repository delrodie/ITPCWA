<?php

namespace App\Form;

use App\Entity\EnRessource;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EnRessourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('reference')
            ->add('titre', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>'off'],
                'label' => 'Title'
            ])
            ->add('description', CKEditorType::class,['attr'=>['class'=>'form-control']])
            ->add('media', FileType::class,[
                'attr'=>['class'=>"dropify-fr", 'data-preview' => ".preview"],
                'label' => "Download document",
                'mapped' => false,
                'multiple' => false,
                'constraints' => [
                    new File([
                        'maxSize' => "20000000m",
                        'mimeTypes' =>[
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                            'image/webp',
                            'application/msword',
                            'application/pdf',
                            'application/vnd.ms-excel',
                            'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ]
                    ])
                ],
                'required' => false
            ])
            //->add('slug')
            //->add('extension')
            //->add('createdAt')
            //->add('updatedAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EnRessource::class,
        ]);
    }
}
