<?php

namespace App\Form;

use App\Entity\Shop;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\ApplicationType;
use App\Form\ProductPictureType;
use App\Repository\ShopRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductType extends ApplicationType
{
    public function __construct(private Security $security){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $category = $options['category'];
        $shop = $options['shop'];

        $builder
            ->add('name', TextType::class)
            ->add('firstPrice', IntegerType::class)
            ->add('quantity', IntegerType::class)
            ->add('lastPrice', IntegerType::class)
            ->add('priceOfShip', IntegerType::class)
            ->add(
                'description', 
                TextareaType::class, [
                    'attr' => [
                        'style' => "height:10rem"
                    ]
                ]
            )
            /*->add(
                'sold', 
                ChoiceType::class,
                $this->getConfiguration('Solde', "d-flex gap-5", [
                    'multiple' => false,
                    'expanded' => true,
                    'choices' => [
                        'OUI' => 1,
                        'NON' => 0,
                    ],
                    'data' => 0,
                ])
            )*/
            ->add(
                'sousCategory', 
                EntityType::class,[
                    'class' => Category::class,
                    'query_builder' => function (CategoryRepository $Crepo) use ($category) {
                        return $Crepo->createQueryBuilder('c')
                        ->select('c')
                        ->where('c.parent = :id')
                        ->setParameter('id', $category)
                        ->orderBy('c.name', 'ASC');
                    },
                    'placeholder' => '-- Sélectionnez --',
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'attr' => [
                        'required' => true,
                    ],
                ]
            )
            ->add(
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
                        'disabled' => 'disabled',
                        'required' => true,
                    ],
                ]
            )
            ->add(
                'images',
                FileType::class, [
                    'label' => false,
                    'multiple' => true,
                    'mapped' => false,
                    'required' => false,
                ]
            )
            //->add('save', SubmitType::class, $this->getConfiguration("Soumettre", "btn btn-primary btn-lg"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'shop' => false,
            'category' => false,
            'data_class' => Product::class,
        ]);
    }
}
