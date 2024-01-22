<?php

namespace App\Form;

use App\Entity\Shop;
use App\Form\ApplicationType;
use App\Entity\ShopValidation;
use App\Repository\ShopRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ShopValidationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $shop = $options['shop'];
        $builder
            ->add(
                'visibility', 
                ChoiceType::class,
                $this->getConfiguration('Livraison', "d-flex gap-5", [
                    'multiple' => false,
                    'expanded' => true,
                    'choices' => [
                        'OUI' => 1,
                        'NON' => 0,
                    ],
                    'data' => 0,
                ])
            )
            ->add(
                'decision',
                ChoiceType::class,
                $this->getConfiguration('Livraison', "d-flex gap-5", [
                    'multiple' => false,
                    'expanded' => true,
                    'choices' => [
                        'Publier' => 'Publier',
                        'Rejeter' => 'Rejeter',
                    ],
                    'data' => 'Rejeter',
                ])
            )
            ->add(
                'description', 
                TextareaType::class,
                $this->getConfiguration(false, "form-control", [
                    'attr' => [
                        'style' => "height:10rem"
                    ]
                ])
            )
            /*->add(
                'shop', 
                EntityType::class, [
                    'class' => Shop::class,
                    'query_builder' => function (ShopRepository $Srepo) use ($shop){
                        return $Srepo->createQueryBuilder('s')
                        ->select('s')
                        ->where('s.slug = :slug')
                        ->setParameter('slug', $shop);
                    },
                    'choice_value' => 'id',
                    'attr' => [
                        'class' => 'form-select',
                        'disabled' => 'disabled',
                        'required' => true,
                    ],
                ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'shop' => false,
            'data_class' => ShopValidation::class,
        ]);
    }
}
