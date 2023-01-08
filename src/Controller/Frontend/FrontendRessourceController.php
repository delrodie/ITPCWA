<?php

namespace App\Controller\Frontend;

use App\Services\GestionCache;
use App\Services\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ressource')]
class FrontendRessourceController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache, private Utility $utility
    )
    {
    }

    #[Route('/{_locale}', name: 'app_frontend_ressource')]
    public function index($_locale): Response
    {
        return $this->render('frontend/ressources.html.twig',[
            'ressources' => $this->gestionCache->cacheRessource($_locale),
            'locale' => $_locale,
            'pagination' => false
        ]);
    }
}
