<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ShopRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserDashboardController extends AbstractController
{
    #[Route('/tableau_de_bord', name: 'app_user_dashboard')]
    public function index(ShopRepository $srepo, ProductRepository $prepo): Response
    {
        $user = $this->getUser();
        $shops = $srepo->findAllShopByUser($user);
        $products = $prepo->findAllProductByUser($user);
        
        return $this->render('user_dashboard/index.html.twig', [
            'shops' => $shops,
            'products' => $products
        ]);
    }

    #[Route('/product_{shopSlug}_{id}/sale', name: 'app_product_sale')]
    public function onSale($shopSlug, Product $product = null, ShopRepository $Srepo, EntityManagerInterface $manager, ProductRepository $Prepo, Request $request): Response
    {
        //Récupère une boutique
        $shop = $Srepo->findOneBy(['slug' => $shopSlug]);
        if($product->isOnSale()){
            $product->setShop($shop)
                    ->setOnSale(0);
            $manager->persist($product);
            $manager->flush();
            return $this->json([
                'code' => 200,
                'message' => 'Pas en vente',
                'onSale' => $product->isOnSale()
            ], 200);
        } else {
            $product->setShop($shop)
                    ->setOnSale(1);
            $manager->persist($product);
            $manager->flush();
            return $this->json([
                'code' => 200,
                'message' => 'En vente',
                'onSale' => $product->isOnSale()
            ], 200);
        }
        return $this->json(['code' => 200, 'message' => 'Ca marche bien'], 200);
    }
}
