<?php

namespace App\Controller;

use App\Entity\Shop;
use App\Form\ShopType;
use App\Service\SendMailService;
use App\Repository\ShopRepository;
use App\Repository\ProductRepository;
use App\Service\Picture\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShopController extends AbstractController
{  
    /**
     * Permet la creation d'une boutique
     */
    #[Route('/boutique/creer', name: 'app_shop_create')]
    public function creat(
        EntityManagerInterface $manager, 
        Request $request, 
        PictureService $pictureService, 
        SendMailService $mail
        ): Response
    {
        $shop = new Shop();
        //On récupère l'utilisateur connecter
        $user = $this->getUser();
        $form = $this->createForm(ShopType::class, $shop);
        $form->handleRequest($request);

        //On verifie son role et son nombre de boutique
        if($user->getRoles() === ['ROLE_USER'] && count($user->getShops()) === 1) {

            $this->addFlash(
                'danger',
                "Ooops!! Mettez à niveau votre plan standard à premium pour plus de boutique."
            );
            //s'il a le role user ayant déjà une boutique, il doit mettre à jour son plan 
            return $this->redirectToRoute('app_plan');

        } else {
            
            // S'il a un role user sans boutique, il peut en créer une.
            // S'il a un role shop_admin, il peut en créer autant qu'il le souhaite
    
            // On vérifie si le formulaire a été soumis et est valide
            if ($form->isSubmitted() && $form->isValid()) {
    
                // On récupère les images
                $image = $form->get('shopPictureFile')->getData();

                // On défini le dossier de destination
                $folder = 'shop/';
                // On appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 640, 400);

                $shop->setAuthor($user)
                     ->setShopPictureName($fichier);
                $manager->persist($shop);
                $manager->flush();

                // On envoie un mail de boutique approuver
                $mail->send(
                    'no-reply@6myshop.com',
                    'rostandngagoum@gmail.com',
                    'Besoin de validation.',
                    'adminShopValidationNew',
                    compact('shop')
                );
                $this->addFlash(
                    'success',
                    "Votre Boutique a été créer avec succès ne sera publié qu'une fois validé par un administrateur après verification."
                );
                return $this->redirectToRoute('app_user_dashboard');
            }
        }
        
        return $this->render('shop/create.html.twig', [
            'form' => $form->createView(),
            'editMode' => false,
        ]);
    }

    /**
     * Permet la modification d'une boutique
     */
    #[Route('/boutique{id}_{shopSlug}/editer', name: 'app_shop_edit')]
    public function edit(
        Shop $shop = null, 
        EntityManagerInterface $manager, 
        Request $request, 
        PictureService $pictureService,
        SendMailService $mail
        ): Response
    {
        //On récupère l'utilisateur connecter
        $user = $this->getUser();
        $form = $this->createForm(ShopType::class, $shop);
        $form->handleRequest($request);

        //On vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
    
            $image = $form->get('shopPictureFile')->getData();

            if($image){
                // On défini le dossier de destination
                $folder = 'shop/';
                // On appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 640, 400);
                $shop->setShopPictureName($fichier);
            }
            $manager->persist($shop);
            $manager->flush();

            // On envoie un mail de boutique approuver
            $mail->send(
                'no-reply@6myshop.com',
                'rostandngagoum@gmail.com',
                'Besoin de validation.',
                'adminShopValidationEdit',
                compact('shop')
            );

            $this->addFlash(
                'success',
                "Les informations de votre boutique ont été modifier avec succès !"
            );
            return $this->redirectToRoute('app_user_dashboard');
        }
        
        return $this->render('shop/create.html.twig', [
            'form' => $form->createView(),
            'editMode' => true,
        ]);
    }

    /**
     * Permet d'afficher un boutique et ces produits
     */
    #[Route('/boutique{shopId}_{shopSlug}/{categorySlug}_sur_{citySlug}', name: 'app_shop_show')]
    public function show($shopSlug, ShopRepository $Srepo,): Response
    {
        //Récupère une boutique
        $shop = $Srepo->findOneShopBySlug($shopSlug);
        
        return $this->render('shop/index.html.twig', [
            'shop' => $shop,
        ]);
    }

    /**
     * Permet la suppression d'image de produit
     */
    #[Route('/suppression/image_boutique/{id}', name: 'app_shop_delete_image', methods: ['DELETE'])]
    public function deleteImage(
        Shop $shop, 
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

        if($this->isCsrfTokenValid('delete' . $shop->getId(), $data['_token'])){
            // Le token csrf est valide
            // On récupère ke non de l'image
            $nom = $shop->getShopPictureName();

            if($pictureService->delete($nom, 'shop/', 640 , 400)){
                // On supprime l'image de la base de donnée
                $shop->setShopPictureName(null);
                $manager->persist($shop);
                $manager->flush();
                return $this->json(['code' => 200, 'success' => true, 'message' => 'l\'image à bien été supprimer'], 200);
            }
            return $this->json(['code' => 400, 'error' => 'erreur de suppression'], 400);
        }
        return $this->json(['code' => 400, 'error' => 'Token invalide'], 400);
    }

    /**
     * 
     */
    #[Route('/suppression_de_la_boutique_{shop}', name: 'app_shop_delete')]
    public function deleteShop(
        Shop $shop, 
        EntityManagerInterface $manager,
        PictureService $pictureService,
        ): Response
    {
        $user = $this->getUser();
        if(!$user) return $this->json([
            'code' => 403,
            'error' => "Désolé !! Vous devez être connecté pour effectuer cette action.",
        ], 403);
        $pictureService->delete($shop->getShopPictureName(), 'shop/', 640 , 400);
        $manager->remove($shop);
        $manager->flush();
        return $this->json(['code' => 200, 'success' => true, 'message' => 'l\'élèment à bien été supprimer'], 200);
    }
}
