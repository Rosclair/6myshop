<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CategoryFixtures extends Fixture
{
    private $counter = 1;

    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory('Agroalimentaire', null, $manager);
        
        $this->createCategory('Produits frais', $parent, $manager);
        $this->createCategory('Produits transformés', $parent, $manager);
        $this->createCategory('Boissons', $parent, $manager);

        $parent = $this->createCategory('Mode', null, $manager);

        $this->createCategory('Homme', $parent, $manager);
        $this->createCategory('Femme', $parent, $manager);
        $this->createCategory('Enfant', $parent, $manager);
             
        $parent = $this->createCategory('Électronique & accessoires', null, $manager);
        
        $this->createCategory('Équipements électroniques', $parent, $manager);
        $this->createCategory('Accessoires électroniques', $parent, $manager);

        $parent = $this->createCategory('Beauté & bien-être', null, $manager);

        $this->createCategory('Soins du corps', $parent, $manager);
        $this->createCategory('Soins du visage', $parent, $manager);
        $this->createCategory('Maquillage', $parent, $manager);        
        
        $parent = $this->createCategory('Artisanat', null, $manager);
        
        $this->createCategory('Objets de décoration', $parent, $manager);
        $this->createCategory('Objets utilitaires', $parent, $manager);

        $manager->flush();
    }

    public function createCategory(string $name, Category $parent = null, ObjectManager $manager)
    {
        $category = new Category();
        $category->setName($name);
        $category->setParent($parent);
        $manager->persist($category);

        $this->addReference('cat-'.$this->counter, $category);
        $this->counter++;

        return $category;
    }
}
