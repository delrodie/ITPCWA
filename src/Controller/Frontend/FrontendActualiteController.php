<?php

namespace App\Controller\Frontend;

use App\Services\GestionCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/page')]
class FrontendActualiteController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache
    )
    {
    }

    #[Route('/{_locale}/{rubrique}', name: 'app_frontend_actualite_index')]
    public function index($_locale): Response
    {
        if ($_locale === 'fr') {
            $actualites = $this->gestionCache->cacheFrActualites(true);
            $traduction = 'news';
        }
        else {
            $actualites = $this->gestionCache->cacheEnActualites(true);
            $traduction = 'actualites';
        }

        return $this->render('frontend/actualites.html.twig',[
            'actualites' => $actualites,
            'locale' => $_locale,
            'traduction' => $traduction,
            'pagination' => false
        ]);
    }

    #[Route('/{_locale}/{rubrique}/{slug}', name: 'app_frontend_actualite_show', methods: ['GET'])]
    public function show($_locale, $slug)
    {
        if ($_locale === 'fr'){
            $actualite = $this->gestionCache->cacheFrActualiteItem($slug, true);
            $traduction = 'news';
        }else{
            $actualite = $this->gestionCache->cacheEnActualiteItem($slug, true);
            $traduction = 'actaulites';
        }

        return $this->render('frontend/actualite_show.html.twig',[
            'actualite' => $actualite,
            'locale' => $_locale,
            'traduction' => $traduction,
        ]);
    }
}
