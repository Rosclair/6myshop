<?php

namespace App\Controller;

use App\Repository\ShopRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * Affiche la page d'acceil
     */
    #[Route('/', name: 'app_main')]
    public function index(ProductRepository $Prepo, ShopRepository $Srepo): Response
    {
        //Permet de récupérer tous les produits 
        $products = $Prepo->findAllProduct();

        //Permet de récupérer toutes les boutiques
        $shops = $Srepo->findAllShop();
        
        return $this->render('main/index.html.twig',[
            'products' => $products,
            'shops' => $shops,
        ]);
    }

    /**
     * Permet d'affiche les produits en lien avec la catégorie selectionnez dans la navbar  
     */
    #[Route('/produit_{categorySlug}_{categoryId}', name: 'app_catList')]
    public function list($categoryId, ProductRepository $Prepo)
    {
        //Récupère toutes les produits d'une catégorie
        $products = $Prepo->findAllProductByCategory($categoryId);

        return $this->render('shop/list.html.twig', [
            'products' => $products,
        ]);
    }

}

