<?php

namespace App\Controller\Frontend;

use App\Repository\EnProjetRepository;
use App\Repository\FrProjetRepository;
use App\Services\GestionCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project')]
class FrontendProjectController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache, private EnProjetRepository $enProjetRepository,
        private FrProjetRepository $frProjetRepository
    )
    {
    }

    #[Route('/{_locale}', name: 'app_frontend_projet_index')]
    public function index($_locale): Response
    {
        if ($_locale === 'fr'){
            // liste des projets en francais mis en cache
            $projets = $this->gestionCache->cacheFrProjet();
        }else{
            // liste des projets en anglais mis en cache
            $projets = $this->gestionCache->cacheEnProjet();
        }

        return $this->render("frontend/projets.html.twig", [
            'projets' => $projets,
            'locale' => $_locale
        ]);
    }

    #[Route('/{_locale}/{slug}', name: 'app_frontend_projet_show', methods: ['GET'])]
    public function show($_locale, $slug)
    {
        if ($_locale === 'fr'){
            // Le projet en francais mis en cache
            $projet = $this->gestionCache->cacheFrProjetItem($slug, true);
        }else{ //dd($slug);
            $projet = $this->gestionCache->cacheEnProjetItem($slug, true); // le projet en anglais mis en cache
        }

        return $this->render("frontend/projet_show.html.twig",[
            'projet' => $projet,
            'locale' => $_locale
        ]);
    }
}
