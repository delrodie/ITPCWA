<?php

namespace App\Form;

use App\Entity\Photo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('media', FileType::class,[
                'attr'=>['class'=>"form-control form-control-lg", 'data-preview' => ".preview"],
                'label' => "Télécharger les photos",
                'mapped' => false,
                'multiple' => true,
                /*'constraints' => [
                    new File([
                        'maxSize' => "2000000m",
                        'mimeTypes' =>[
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                            'image/webp',
                        ]
                    ])
                ],*/
                'required' => false,
                'help' => "Attention, le poids total doit être inférieur ou égal à 20MB"
            ])
            //->add('album')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
        ]);
    }
}
