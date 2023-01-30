<?php

namespace App\Controller\Frontend;

use App\Services\GestionCache;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/team')]
class FrontendEquipeController extends AbstractController
{
    public function __construct(Private GestionCache $gestionCache, private Flasher $flasher)
    {
    }

    #[Route('/{_locale}/', name: 'app_frontend_equipe_index')]
    public function index($_locale): Response
    {
        return $this->render('frontend/equipes.html.twig',[
            'locale' => $_locale,
            'active' => "presentation",
            'equipes' => $this->gestionCache->cacheEquipe($_locale)
        ]);
    }

    #[Route('/{_locale}/{slug}', name: 'app_frontend_equipe_show')]
    public function show($_locale, $slug)
    {
        return $this->render('frontend/equipe_membre.html.twig',[
            'locale' => $_locale,
            'active' => "presentation",
            'equipe' => $this->gestionCache->cacheEquipeItem($_locale, $slug)
        ]);
    }
}
