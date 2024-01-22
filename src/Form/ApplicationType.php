<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;


class ApplicationType extends AbstractType
{

    /**
     * permet d'avoire une configuration de base d'un champ !
     *
     * @param string $label
     * @param string $placeholder
     * @param string $class
     * @param array $options
     * @return array
     */
    protected function getConfiguration($placeholder, $class, $options = [])
    {
        return array_merge([
            'attr' => [
                'placeholder' => $placeholder,
                'class' => $class,
            ]
        ], $options);
    }

}