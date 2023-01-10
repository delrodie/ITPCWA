<?php

namespace App\Controller\Frontend;

use App\Repository\MaintenanceExceptRepository;
use App\Repository\MaintenanceRepository;
use App\Services\GestionCache;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache, private MaintenanceRepository $maintenanceRepository,
        private MaintenanceExceptRepository $exceptRepository
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{_locale<en|fr>}', name: 'app_frontend_index')]
    public function index(Request $request, $_locale): Response
    {
        //Mise en maintenance
        if ($this->maintenanceRepository->findOneBy(['statut' => true],['id'=>"DESC"])) {

            $exception = $this->exceptRepository->findOneBy([
                'ip' => $request->server->get('REMOTE_ADDR'),
                'statut' => true
            ]);

            if (!$exception)
                return $this->redirectToRoute('app_maintenance');
        }

        return $this->render('frontend/index.html.twig', [
            'slides' => $this->gestionCache->cacheSlides(),
            'locale' => $_locale,
            'actualites' => $this->gestionCache->cacheActualites($_locale),
            'projet' => $this->gestionCache->cacheLastProjet($_locale),
            'axe' => $this->gestionCache->cacheItemPresentation($_locale, 'axe'),
            'presentation' => $this->gestionCache->cacheItemPresentation($_locale, 'presentation'),
            'zone' => $this->gestionCache->cacheItemPresentation($_locale, 'intervention'),
            'bienvenue' => $this->gestionCache->cacheBienvenue($_locale),
            'active' => 'accueil',
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{_locale}/message', name: 'app_frontend_message')]
    public function message($_locale)
    {
        return $this->render('frontend/messages.html.twig',[
            'messages' => $this->gestionCache->cacheMessages($_locale)
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
