<?php

namespace App\Form;

use App\Entity\EnPresentation;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EnPresentationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>'off'],
                'label' => "Title"
            ])
            //->add('resume')
            ->add('contenu', CKEditorType::class,[
                'attr'=>['class'=>'form-control'],
                'label' => "Content"
            ])
            ->add('media', FileType::class,[
                'attr'=>['class'=>"dropify", 'data-preview' => ".preview"],
                'label' => "Download illustration",
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
            //->add('slug')
            ->add('tags', TextType::class,[
                'attr'=>['class'=>'form-control', 'data-role'=>'tagsinput'],
                'label' => "Tags"
            ])
            //->add('createdAt')
            //->add('updatedAt')
            ->add('type', null, ['attr'=>['class'=>'form-select']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EnPresentation::class,
        ]);
    }
}
