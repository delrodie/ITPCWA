<?php

namespace App\Controller\Frontend;

use App\Services\GestionCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sitemap')]
class FrontendSitemapController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache
    )
    {
    }

    #[Route('/{_locale}', name: 'app_sitemap')]
    public function index($_locale): Response
    {

        return $this->render('frontend/sitemap.html.twig',[
            'locale' => $_locale,
            'rubriques' => $this->gestionCache->cacheType($_locale),
            'active' => 'accueil'
        ]);
    }
}
