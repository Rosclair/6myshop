<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Shop;
use App\Entity\User;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ShopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('contact', IntegerType::class)
            ->add('district', TextType::class)
            ->add('shopPictureFile', FileType::class, [
                'label' => false,
                'multiple' => false,
                'mapped' => false,
                'required' => false,
            ])
            ->add(
                'city',
                EntityType::class,
                [
                    'class'       => City::class,
                    'placeholder' => '-- Sélectionnez --',
                    'attr' => [
                        'class' => 'form-select',
                    ]
                ]
            )
            ->add(
                'category', 
                EntityType::class,
                [
                    'class' => Category::class,
                    'query_builder' => function (CategoryRepository $Crepo) {
                        return $Crepo->createQueryBuilder('c')
                        ->select('c')
                        ->where('c.parent IS NULL')
                        ->orderBy('c.name', 'ASC');
                    },
                    'placeholder' => '-- Sélectionnez --',
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'attr' => [
                        'class' => 'form-select',
                        'required' => true,
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Shop::class,
        ]);
    }
}
