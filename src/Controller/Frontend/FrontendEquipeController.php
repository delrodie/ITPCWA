<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/team')]
class FrontendEquipeController extends AbstractController
{
    #[Route('/{_locale}/', name: 'app_frontend_equipe_index')]
    public function index($_locale): Response
    {
        return $this->render('frontend/equipes.html.twig',[
            'locale' => $_locale,
            'active' => "presentation"
        ]);
    }
}
