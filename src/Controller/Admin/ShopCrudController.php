<?php

namespace App\Controller\Admin;

use App\Entity\Shop;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ShopCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Shop::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            //TextField::new('shopPictureFile')->setFormType(VichImageType::class)->hideOnIndex(),
            ImageField::new('shopPictureName')->setBasePath('/build/images/upload/shop')->OnlyOnIndex(),
            TextField::new('name'),
            TextField::new('district')->hideOnIndex(),
            IntegerField::new('contact'),
            AssociationField::new('city'),
            AssociationField::new('author'),
            AssociationField::new('category'),
            AssociationField::new('products')->hideOnForm(),
            AssociationField::new('shopValidation'),
            DateField::new('createdAt')->hideOnForm(),
            SlugField::new('slug')->setTargetFieldName('name')->hideOnIndex(),
        ];
    }
}
