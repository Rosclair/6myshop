<?php

namespace App\Controller;

use App\Entity\ShopValidation;
use App\Form\ShopValidationType;
use App\Service\SendMailService;
use App\Repository\ShopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPER_ADMIN')]
class ShopValidationController extends AbstractController
{
    /**
     * Permet d'afficher les boutiques qui doivent être vérifier
     */
    #[Route('/admin/validation', name: 'app_admin_validation')]
    public function index(ShopRepository $repo): Response
    {
        //Récupàre toutes les boutique qui n'ont pas encore été vérifier par un admin
        $shopsNoValidate = $repo->findAllShopByShopValidationToValidate();

        //Récupàre toutes les boutique qui ont été vérifier mais rejeter par un admin
        $shopsDismiss = $repo->findAllShopByShopValidationDismiss();

        $shopReEdit = $repo->findAllShop();

        return $this->render('admin/shop_validation/index.html.twig', [
            'shopsNoValidate' => $shopsNoValidate,
            'shopsDismiss' => $shopsDismiss,
            'shopReEdit' => $shopReEdit,
        ]);
    }

    /**
     * Permet la création d'une validation après vérification par un admin
     */
    #[Route('/admin/validation/{shopSlug}_{id}/edit', name: 'app_admin_validation_edit')]
    #[Route('/admin/validation/{shopSlug}/creer', name: 'app_admin_validation_creat')]
    public function creat(
        ShopValidation $shopValidation = null, 
        $shopSlug, 
        ShopRepository $repo, 
        Request $request, 
        EntityManagerInterface $manager, 
        SendMailService $mail
        ): Response
    {
        if(!$shopValidation){
            $shopValidation = new ShopValidation();
        }
        
        $user = $this->getUser();
        $shop = $repo->findOneBy(['slug' => $shopSlug]);
        $form = $this->createForm(ShopValidationType::class, $shopValidation, ['shop' => $shopSlug]);
        $form->handleRequest($request);

        // Validation des donnée
        if ($form->isSubmitted() && $form->isValid()) {

            if (!$shopValidation->getId()) {

                $shopValidation->setAuthor($user)
                                ->setShop($shop);
                $manager->persist($shopValidation);
                $manager->flush();

                // On verifie la decision rendu
                if($shopValidation->isVisibility()){
                    // On envoie un mail de boutique approuver
                    $mail->send(
                        'no-reply@6myshop.com',
                        $shop->getAuthor()->getEmail(),
                        'Votre Boutique à été approuvée.',
                        'shopValidationSuccess',
                        compact('shop')
                    );
                }else{
                    // On envoie un mail de boutique rejeter
                    $mail->send(
                        'no-reply@6myshop.com',
                        $shop->getAuthor()->getEmail(),
                        'Votre Boutique à été rejetée.',
                        'shopValidationDimiss',
                        compact('shop', 'shopValidation')
                    );
                }
                $this->addFlash(
                    'success',
                    "Décision enregistrer avec success avec un mail envoyeé à l'utilisateur."
                );
                return $this->redirectToRoute('app_admin_validation');

            } elseif ($shopValidation->getId()) {
                
                $shopValidation->setAuthor($user)
                                ->setShop($shop);
                $manager->persist($shopValidation);
                $manager->flush();

                // On verifie la decision rendu
                if($shopValidation->isVisibility()){
                    // On envoie un mail de boutique approuver
                    $mail->send(
                        'no-reply@6myshop.com',
                        $shop->getAuthor()->getEmail(),
                        'Votre Boutique à été approuvée.',
                        'shopValidationSuccess',
                        compact('shop')
                    );
                }else{
                    // On envoie un mail de boutique rejeter
                    $mail->send(
                        'no-reply@6myshop.com',
                        $shop->getAuthor()->getEmail(),
                        'Votre Boutique à été rejetée.',
                        'shopValidationDimiss',
                        compact('shop', 'shopValidation')
                    );
                }

                $this->addFlash(
                    'success',
                    "Décision enregistrer avec success avec un mail envoyeé à l'utilisateur."
                );
                return $this->redirectToRoute('app_admin_validation');
            }
        }
        return $this->render('admin/shop_validation/create.html.twig', [
            'form' => $form->createView(),
            'editMode' => $shopValidation->getId() !== null,
        ]);
    }

    /**
     * Permet d'afficher la page des differnt plan
     */
    #[Route('/plan', name: 'app_plan')]
    public function plan()
    {
        return $this->render('shop/plan.html.twig');
    }
}
