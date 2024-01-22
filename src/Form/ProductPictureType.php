<?php

namespace App\Form;

use App\Entity\ProductPicture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProductPictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'urlFile',
                FileType::class, [
                    'label' => false,
                    'required' => false,
                    'delete_label' => 'Supprimer?',
                    'download_label' => 'Télécharger',
                    'imagine_pattern' => 'miniature_product_form',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductPicture::class,
        ]);
    }
}
