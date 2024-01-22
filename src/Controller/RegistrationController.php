<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JWTService;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    /**
     * Permet l'inscription de nouveau utilisateur
     */
    #[Route('/inscription', name: 'app_registration')]
    public function index(EntityManagerInterface $manager, Request $request, UserPasswordHasherInterface $encoder, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if(!$user->getId()){

                $hash = $encoder->hashPassword($user, $user->getPassword());
                $manager->persist($user->setPassword($hash));
                $manager->flush();

                // On génère le JWT de l'utilisateur
                // On crée le Header
                $header = [
                    'typ' => 'JWT',
                    'alg' => 'HS256'
                ];

                // On créer le payload
                $payload = [
                    'user_id' => $user->getId()
                ];

                // On génère le token
                $token = $jwt->generate($header, $payload,
                $this->getParameter('app.jwtsecret'));

                // On envoie un mail
                $mail->send(
                    'no-reply@6myshop.com',
                    $user->getEmail(),
                    'Activation de votre compte.',
                    'registration',
                    compact('user', 'token')
                );
    
                $this->addFlash(
                    'success',
                    "Vous avez été inscrit(e) avec succès."
                );
                return $this->redirectToRoute('app_login');

            } elseif ($user) {

                $hash = $encoder->hashPassword($user, $user->getPassword());
                $manager->persist($user->setPassword($hash));
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Vos modifications ont été enregistrer avec succès !"
                );

                return $this->redirectToRoute('app_login');
            }
        }
        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet de vérifier que le token est valide
     */
    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser($token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $manager): Response
    {
        //On vérifie si le token est valide, n'a pas expiré et n'a pas été modifié
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))){
            // On récupère le payload
            $payload = $jwt->getPayload($token);

            // On récupère le user du token
            $user = $userRepository->find($payload['user_id']);

            //On vérifie que l'utilisateur existe et n'a pas encore activé son compte
            if($user && !$user->IsIsVerified()){
                $user->setIsVerified(true);
                $manager->flush($user);
                $this->addFlash(
                    'success',
                    "Votre compte a été activé avec success !"
                );
                return $this->redirectToRoute('app_login');
            }
        }
        // Ici un problème se pose dans le token
        $this->addFlash(
            'danger',
            "Le token est invalide ou a expiré !"
        );
        return $this->redirectToRoute('app_login');
    }

    #[Route('/renvoiverif', name: 'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        if(!$user){
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login');    
        }

        if($user->IsIsVerified()){
            $this->addFlash('warning', 'Votre compte est déjà activé');
            return $this->redirectToRoute('app_login');    
        }

        // On génère le JWT de l'utilisateur
        // On crée le Header
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        // On crée le Payload
        $payload = [
            'user_id' => $user->getId()
        ];

        // On génère le token
        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        // On envoie un mail
        $mail->send(
            'no-reply@6myshop.com',
            $user->getEmail(),
            'Activation de votre compte.',
            'registration',
            compact('user', 'token')
        );
        $this->addFlash('success', 'Email de vérification envoyé');
        return $this->redirectToRoute('app_login');
    }
}
