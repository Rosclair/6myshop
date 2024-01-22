<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\City;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\ProductPicture;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\CategoryFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $encoder;
    /**
     * Undocumented function
     *
     * @param UserPasswordHasherInterface $encoder
     */
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        //city
        $cities = [];
        for ($s = 0; $s < 3; $s++) {
            $city = new City();
            
            if ($s === 0) {
                $cityName = 'Bafoussam';
            } elseif ($s === 1) {
                $cityName = 'Yaoundé';
            } elseif ($s === 2) {
                $cityName = 'Douala';
            }

            $city->setName($cityName);
            $manager->persist($city);
            $cities[] = $city;
        }

        //User
        $users = [];
        for ($s = 0; $s < 10; $s++) {
            $user = new User();
            $role = $faker->randomElement($array = array(["ROLE_SUPER_ADMIN"], ["ROLE_SHOP_ADMIN"], ["ROLE_USER"],));
                
            $user->setEmail($faker->freeEmail())
                ->setRoles($role)
                ->setFullname($faker->firstname)
                ->setPassword($this->encoder->hashPassword($user, 'Test2023'));
            $manager->persist($user);
            $users [] = $user;
        }
        //Shop
        for ($id = 0; $id < 50; $id++){
            $shop = new Shop();
            
            $district = $faker->randomElement($array = array('Socada', 'Marché A', 'Marché B', 'Tamdja', 'Derrière la Pelouse', 'Carefour le maire', 'Derrière les finances', 'Madelon'));
            $img =  $faker->randomElement($array = array('1.jpg','2.jpg','3.jpg','4.jpg','5.jpg'));
            //On va chercher une référence de category
            $category = $this->getReference('cat-'. rand(1, 18));
            $user = $users[mt_rand(0, count($users) - 1)];

            if($category->getParent() === null){
                $shop->setAuthor($user)
                    ->setCity($city)
                    ->setName($faker->company)
                    ->setContact('679742319')
                    ->setShopPictureName($img)
                    ->setDistrict($district)
                    ->setCategory($category);
                $manager->persist($shop);
                
                //Product
                for ($p = 0; $p < 500; $p++){
                    
                    $category = $this->getReference('cat-'. rand(1, 18));

                    if($shop->getCategory('id') === $category->getparent()){

                        $pro = new Product();
                        $price = $faker->randomElement($array = array('5000', '8000', '11000', '14000', '17000', '21000', '100000', '150000', '200000', ));
                        $price1 = $price - 3000;
                        $description = $faker->realText($maxNbChars = 200, $indexSize = 2);
                        $sold = $faker->randomElement($array = array('0', '1'));
                        //$c =  $faker->randomElement($array = array('1.png','2.png','3.png','4.png','5.png','6.png','7.png'));
                        if($sold === 1){
                            $price = null;
                            $price1 = $faker->randomElement($array = array('5000', '8000', '11000', '14000', '17000', '21000', '100000', '150000', '200000', ));
                        }

                        $pro->setName($faker->name)
                            ->setAuthor($user)
                            ->setDescription($description)
                            ->setQuantity(mt_rand(1, 100))
                            ->setFirstPrice($price)
                            ->setLastPrice($price1)
                            ->setpriceOfShip($faker->randomElement($array = array('500', '1000')))
                            ->setShop($shop)
                            ->setSousCategory($category);
                        $manager->persist($pro);

                        //ProductPicture
                        for ($pp = 0; $pp < mt_rand(1, 5); $pp++) {
                            $imageProduct = new ProductPicture();

                            $urlName =  $faker->randomElement($array = array('1.png','2.png','3.png','4.png','5.png','6.png','7.png'));
                            
                            $imageProduct->setUrlName($urlName)
                                ->setProduct($pro);
                            $manager->persist($imageProduct);
                        }
                    }
                }
            }

        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class
        ];  
    }
}
