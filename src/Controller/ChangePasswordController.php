<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ChangePasswordController extends AbstractController
{
    #[Route('/change/password', name: 'app_change_password')]
    public function index(Request $request, AuthenticationUtils $authenticationUtils, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    {
        // Verification de la connexion de l'utilisateur
        $last_username = $authenticationUtils->getLastUsername(); //dd($last_username);
        if (!$last_username){
            $this->addFlash('danger', "Attention veuillez vous connecter pour changer le mot de passe.");
            return $this->redirectToRoute('app_login',[],Response::HTTP_SEE_OTHER);
        }

        // Recuperation des informations du formulaire
        $username = $request->request->get('_username');
        $password = $request->request->get('_password');

        // Modification du mot de passe si c'est le même utilisateur
        if ($username === $last_username && $request->request->get('_csrf_token')){
            $user = $userRepository->findOneBy(['email' => $username]);
            $passwordHashed = $passwordHasher->hashPassword($user, $password);

            $userRepository->upgradePassword($user, $passwordHashed);

            $this->addFlash('success', "Le mot de passe a été modifié avec succès!");

            return $this->redirectToRoute('app_logout');
        }

        return $this->render('security/change_password.html.twig',[
            'last_username' => $last_username,
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }
}
