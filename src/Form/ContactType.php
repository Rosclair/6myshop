<?php

namespace App\Form;

use App\Data\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class, [
                    'label' => false,
                    'required' => true,
                    'attr' => [
                        'class' => "form-control",
                        'id' => "name"
                    ]
                ]
            )
            ->add(
                'email',
                EmailType::class, [
                    'label' => false,
                    'required' => true,
                    'attr' => [
                        'class' => "form-control",
                        'id' => "email"
                    ]
                ]
            )
            ->add(
                'phone',
                SearchType::class, [
                    'label' => false,
                    'required' => true,
                    'attr' => [
                        'class' => "form-control",
                        'id' => "phone"
                    ]
                ]
            )
            ->add(
                'message',
                TextareaType::class, [
                    'label' => false,
                    'required' => true,
                    'attr' => [
                        'class' => "form-control",
                        'id' => "message",
                        'style' => "height:12.8rem"
                    ]
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => Contact::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
}
