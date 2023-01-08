<?php

namespace App\Controller\Frontend;

use App\Services\GestionCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/footer')]
class FrontendFooterController extends AbstractController
{
    public function __construct(private GestionCache $gestionCache)
    {
    }

    #[Route('/{_locale}', name: 'app_frontend_footer')]
    public function index($_locale): Response
    {
        return $this->render('frontend/footer_presentation.html.twig',[
            'presentation' => $this->gestionCache->cacheItemPresentation($_locale, 'presentation')
        ]);
    }

    #[Route('/{_locale}/menu', name: 'app_frontend_footer_menu')]
    public function footer($_locale): Response
    {
        return $this->render('frontend/footer.html.twig',[
            'rubriques' => $this->gestionCache->cacheType($_locale),
            'locale' => $_locale
        ]);
    }
}
