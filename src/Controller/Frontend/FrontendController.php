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
            'locale' => $_locale,
            'actualites' => $this->gestionCache->cacheActualites($_locale),
            'projet' => $this->gestionCache->cacheLastProjet($_locale),
            'axe' => $this->gestionCache->cacheItemPresentation($_locale, 'axe', true),
            'presentation' => $this->gestionCache->cacheItemPresentation($_locale, 'presentation', true)
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

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{_locale}/menu/type', name: 'app_frontend_menu')]
    public function menu($_locale)
    {

        return $this->render('frontend/menu.html.twig',[
            'rubriques' => $this->gestionCache->cacheType($_locale),
            'locale' => $_locale,
        ]);
    }

}
