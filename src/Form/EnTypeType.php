<?php

namespace App\Form;

use App\Entity\EnType;
use App\Entity\FrType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class,['attr'=>['class'=>'form-control', 'autocomplete'=>"off"]])
            /*->add('traduction', EnTypeType::class,[
                'attr'=>['attr'=>'form-control'],
                'class' => FrType::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('ft')
                        ->where('ft.pageIndex is null')
                        ->orderBy('ft.titre', "ASC");
                },
                'choice_label' => 'titre'
            ])*/
            //->add('slug')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EnType::class,
        ]);
    }
}
