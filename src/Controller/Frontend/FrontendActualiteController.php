<?php

namespace App\Controller\Frontend;

use App\Services\GestionCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/page')]
class FrontendActualiteController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache, private TranslatorInterface $translator
    )
    {
    }

    #[Route('/{_locale}/{rubrique}', name: 'app_frontend_actualite_index')]
    public function index($_locale): Response
    {
        if ($_locale === 'fr')$traduction = 'news';
        else $traduction = 'actualites';

        return $this->render('frontend/actualites.html.twig',[
            'actualites' => $this->gestionCache->cacheActualites($_locale),
            'locale' => $_locale,
            'traduction' => $traduction,
            'pagination' => false,
            'active' => 'actualite'
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
            $traduction = 'actualites';
        }

        return $this->render('frontend/actualite_show.html.twig',[
            'actualite' => $actualite,
            'locale' => $_locale,
            'traduction' => $traduction,
            'active' => "actualite"
        ]);
    }
}
