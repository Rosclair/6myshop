<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                $this->getConfiguration("Email", "input", [
                    'required' => true,
                ])
            )
            ->add(
                'password',
                PasswordType::class,
                $this->getConfiguration("Mot de passe", "me-2", [
                    'required' => true,
                ])
            )
            ->add(
                'passwordConfirm',
                PasswordType::class,
                $this->getConfiguration("Confimer le mot de passe", "me-2", [
                    'required' => true,
                ])
            )
            ->add(
                'fullname',
                TextType::class,
                $this->getConfiguration("Noms complet", "input", [
                    'required' => true,
                ])
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
