<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\ProductPicture;
use App\Repository\ShopRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Service\Picture\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * Permet la creation d'un produit
     */
    #[Route('/boutique_{shopSlug}/{categoryId}/creer_un_produit', name: 'app_product_create')]
    public function creat(
        $categoryId, $shopSlug, 
        EntityManagerInterface $manager, 
        CategoryRepository $Crepo, 
        ShopRepository $Srepo, 
        Request $request, 
        PictureService $pictureService
        ): Response
    {
        $product = new Product();
        $user = $this->getUser();
        //On récupère une boutique
        $shop = $Srepo->findOneBy(['slug' => $shopSlug]);
        //On récupère sa catégorie
        $category = $Crepo->findBy(['parent' => $categoryId]);
        $productForm = $this->createForm(ProductType::class, $product, ['shop' => $shopSlug, 'category' => $categoryId]);
        $productForm->handleRequest($request);

        if($user->getRoles() === ['ROLE_USER'] && count($user->getProducts()) === 50) {

            $this->addFlash(
                'danger',
                "Ooops!! Mettez à niveau votre plan standard à premium pour plus de produit."
            );
            return $this->redirectToRoute('app_plan');

        } else {

            // Validation des donnée
            if ($productForm->isSubmitted() && $productForm->isValid()) {
                
                // On récupère les images
                $images = $productForm->get('images')->getData();

                //On sauvegade chaque image du formulaire en BD ->getClientOriginalName()
                foreach($images as $image){
                    // On défini le dossier de destination
                    $folder = 'product/';

                    // On appelle le service d'ajout
                    $fichier = $pictureService->add($image, $folder, 600, 600);

                    $img = new ProductPicture();
                    $img->setUrlName($fichier);
                    $product->addImage($img);
                }

                $product->setShop($shop)
                        ->setAuthor($user);
                $manager->persist($product);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "L'annonce a été créer avec succès !"
                );
                return $this->redirectToRoute('app_user_dashboard');
            }
        }

        return $this->render('product/create.html.twig', [
            'productForm' => $productForm->createView(),
            'editMode' =>  false,
        ]);
    }
    
    /**
     * Permet la modification d'un produit
     */
    #[Route('/boutique_{shopSlug}/{categoryId}/{slug}_{id}/editer_un_produit', name: 'app_product_edit')]
    public function edit(
        Product $product = null, 
        $categoryId, $shopSlug, 
        EntityManagerInterface $manager, 
        CategoryRepository $Crepo, 
        ShopRepository $Srepo, 
        Request $request, 
        PictureService $pictureService
        ): Response
    {
        $user = $this->getUser();
        //On récupère une boutique
        $shop = $Srepo->findOneBy(['slug' => $shopSlug]);
        //On récupère sa catégorie
        $category = $Crepo->findBy(['parent' => $categoryId]);
        $productForm = $this->createForm(ProductType::class, $product, ['shop' => $shopSlug, 'category' => $categoryId]);
        $productForm->handleRequest($request);

        // Validation des données
        if ($productForm->isSubmitted() && $productForm->isValid()) {
                // On récupère les images
                $images = $productForm->get('images')->getData();

                //On sauvegade chaque image du formulaire en BD ->getClientOriginalName()
                foreach($images as $image){
                    // On défini le dossier de destination
                    $folder = 'product/';

                    // On appelle le service d'ajout
                    $fichier = $pictureService->add($image, $folder, 600, 600);

                    $img = new ProductPicture();
                    $img->setUrlName($fichier);
                    $product->addImage($img);
                }

            $product->setShop($shop)
                    ->setAuthor($user);
            $manager->persist($product);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce a été modifier avec succès !"
            );
            return $this->redirectToRoute('app_user_dashboard');
        }

        return $this->render('product/create.html.twig', [
            'productForm' => $productForm->createView(),
            'editMode' => true,
        ]);
    }

    /**
     * Permet d'afficher un produit et ces produits
     */
    #[Route('/boutique_{shopSlug}/{categorySlug}/{cityId}/{productSlug}_{productId}', name: 'app_product_detail')]
    public function show(
        $cityId, 
        $shopSlug, 
        $categorySlug, 
        $productSlug, 
        $productId, 
        ProductRepository $Prepo
        ): Response
    {
        //Récupère un produit en fonction de son id et du slug
        $product = $Prepo->findOneProductById($productId, $productSlug);

        //Récupère toutes les produits en fontione d'une ville et de sa sousCatégorie
        $products = $Prepo->findAllProductByCategoryAndCity($categorySlug, $cityId);

        return $this->render('product/index.html.twig', [
            'product' => $product,
            'products' => $products,
        ]);
    }

    /**
     * Permet la suppression d'image de produit
     */
    #[Route('/suppression/image_produit/{id}', name: 'app_product_delete_image', methods: ['DELETE'])]
    public function deleteImage(
        ProductPicture $productPicture, 
        EntityManagerInterface $manager, 
        Request $request, 
        PictureService $pictureService
        ): Response
    {
        $user = $this->getUser();
        if(!$user) return $this->json([
            'code' => 403,
            'message' => "Désolé !! Vous devez être connecté pour effectuer cette action.",
        ], 403);

        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('delete' . $productPicture->getId(), $data['_token'])){
            // Le token csrf est valide
            // On récupère ke non de l'image
            $nom = $productPicture->getUrlName();

            if($pictureService->delete($nom, 'product/', 600 , 600)){
                // On supprime l'image de la base de donnée
                $manager->remove($productPicture);
                $manager->flush();
                return $this->json(['code' => 200, 'success' => true, 'message' => 'l\'image à bien été supprimer'], 200);
            }
            return $this->json(['code' => 400, 'message' => 'erreur de suppression'], 400);
        }
        return $this->json(['code' => 400, 'message' => 'Token invalide'], 400);
    }

    /**
     * 
     */
    #[Route('/suppression_du_produit_{product}', name: 'app_product_delete')]
    public function deleteProduct(
        Product $product, 
        EntityManagerInterface $manager,
        PictureService $pictureService,
        ): Response
    {
        $user = $this->getUser();
        if(!$user) return $this->json([
            'code' => 403,
            'error' => "Désolé !! Vous devez être connecté pour effectuer cette action.",
        ], 403);
        
        foreach($product->getImages() as $image){
            $pictureService->delete($image->getUrlName(), 'product/', 600 , 600);
        }

        $manager->remove($product);
        $manager->flush();
        return $this->json(['code' => 200, 'success' => true, 'message' => 'l\'élèment à bien été supprimer'], 200);
    }
}