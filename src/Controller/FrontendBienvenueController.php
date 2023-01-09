<?php

namespace App\Controller;

use App\Services\GestionCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('welcome')]
class FrontendBienvenueController extends AbstractController
{
    #[Route('/{_locale}', name: 'app_frontend_bienvenue')]
    public function index($_locale, GestionCache $gestionCache): Response
    {
        return $this->render('frontend/bienvenue.html.twig',[
            'bienvenue' => $gestionCache->cacheBienvenue($_locale),
            'locale'=> $_locale,
            'active' => "accueil"
        ]);
    }
}
