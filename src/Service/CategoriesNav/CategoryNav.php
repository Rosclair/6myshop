<?php

namespace App\Service\CategoriesNav;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpKernel\KernelInterface;

class CategoryNav
{
    public function getCategory(CategoryRepository $Crepo)
    {
        $categories = $Crepo->findAll();
        return $categories;
    }

}