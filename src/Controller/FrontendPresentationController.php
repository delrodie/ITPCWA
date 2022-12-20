<?php

namespace App\Controller;

use App\Services\GestionCache;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendPresentationController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    #[Route('/{_locale}/{slug}', name: 'app_frontend_presentation', methods: ['GET'])]
    public function index($_locale, $slug): Response
    {

        if ($_locale === 'fr')
            $presentation = $this->gestionCache->cacheFrPresentation($slug);
        else
            $presentation = $this->gestionCache->cacheEnPresentation($slug);

        //dd($presentation);

        if (!$presentation) {
            return $this->render('frontend/presentation_404.html.twig',[
                'locale' => $_locale
            ]);
        }

        return $this->render('frontend/presentation.html.twig',[
            'presentation' => $presentation,
            'locale' => $_locale
        ]);
    }
}
