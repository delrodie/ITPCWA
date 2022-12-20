<?php

namespace App\Controller\Frontend;

use App\Services\GestionCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache
    )
    {
    }

    #[Route('/{_locale}/sitemap', name: 'app_sitemap')]
    public function index($_locale): Response
    {
        if ($_locale === 'fr')
            $rubriques = $this->gestionCache->cacheFrType();
        else
            $rubriques = $this->gestionCache->cacheEnType();

        return $this->render('frontend/sitemap.html.twig',[
            'locale' => $_locale,
            'rubriques' => $rubriques
        ]);
    }
}
