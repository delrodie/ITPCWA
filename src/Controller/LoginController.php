<?php

namespace App\Controller;

use App\Services\GestionUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/security', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils, GestionUser $_user): Response
    {
        if ($_user->initialisation())
            $this->addFlash('success', "L'utilisateur ITPCWA a été crée avec succès!");

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        throw new \Exception('Deconnexion');
    }
}
