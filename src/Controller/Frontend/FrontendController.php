<?php

namespace App\Controller\Frontend;

use App\Services\GestionCache;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{_locale<en|fr>}', name: 'app_frontend_index')]
    public function index($_locale): Response
    {
        return $this->render('frontend/index.html.twig', [
            'slides' => $this->gestionCache->cacheSlides(),
            'locale' => $_locale
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{_locale}/message', name: 'app_frontend_message')]
    public function message($_locale)
    {
        if ($_locale === 'fr')
            $messages = $this->gestionCache->cacheFrMessages();
        else
            $messages = $this->gestionCache->cacheEnMessage();

        return $this->render('frontend/messages.html.twig',[
            'messages' => $messages
        ]);
    }

    #[Route('/{_locale}/menu/type', name: 'app_frontend_menu')]
    public function menu($_locale)
    {
        if ($_locale === 'fr')
            $rubriques = $this->gestionCache->cacheFrType();
        else
            $rubriques = [];

        return $this->render('frontend/menu.html.twig',[
            'rubriques' => $rubriques
        ]);
    }
}
