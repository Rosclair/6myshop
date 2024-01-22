<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder            
            ->add(
                'oldpassword',
                PasswordType::class,
                $this->getConfiguration("Mot de passe actuel", "me-2", [
                    'required' => true,
                ])
            )
            ->add(
                'newpassword',
                PasswordType::class,
                $this->getConfiguration("Nouveau mot de passe", "me-2", [
                    'required' => true,
                ])
            )            
            ->add(
                'passwordConfirm',
                PasswordType::class,
                $this->getConfiguration("Confirmer le nouveau mot de passe", "me-2", [
                    'required' => true,
                ])
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
