<?php

namespace App\Controller\Frontend;

use App\Services\GestionCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/multimedia')]
class FrontendMultimediaController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache
    )
    {
    }

    #[Route('/{_locale}/{rubrique}', name: 'app_frontend_multimedia', methods: ['GET','POST'])]
    public function index(Request $request, $_locale): Response
    {
        return $this->render('frontend/multimedias.html.twig',[
            'locale' => $_locale,
            'albums' => $this->gestionCache->cacheAlbum($_locale)
        ]);
    }

    #[Route('/{_locale}/{rubrique}/{slug}', name: 'app_frontend_multimedia_show', methods: ['GET','POST'])]
    public function show(Request $request, $_locale, $slug)
    {
        return $this->render('frontend/multimedia.html.twig',[
            'locale' => $_locale,
            'album' => $this->gestionCache->cacheAlbumItem($_locale, $slug)
        ]);
    }
}
