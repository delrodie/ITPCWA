<?php

namespace App\Controller\Frontend;

use App\Repository\EnProjetRepository;
use App\Repository\FrProjetRepository;
use App\Services\GestionCache;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project')]
class FrontendProjectController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache, private EnProjetRepository $enProjetRepository,
        private FrProjetRepository $frProjetRepository, private PaginatorInterface $paginator
    )
    {
    }

    #[Route('/{_locale}', name: 'app_frontend_projet_index')]
    public function index(Request $request, $_locale): Response
    {
        $datas = $this->gestionCache->cacheProjets($_locale);
        $projets = $this->paginator->paginate(
          $datas,
          $request->query->getInt('page', 1),
          6
        );

        return $this->render("frontend/projets.html.twig", [
            'projets' => $projets,
            'locale' => $_locale,
            'active' => 'projet'
        ]);
    }

    #[Route('/{_locale}/{slug}', name: 'app_frontend_projet_show', methods: ['GET'])]
    public function show($_locale, $slug)
    {
        if ($_locale === 'fr'){
            // Le projet en francais mis en cache
            $projet = $this->gestionCache->cacheFrProjetItem($slug);
        }else{ //dd($slug);
            $projet = $this->gestionCache->cacheEnProjetItem($slug); // le projet en anglais mis en cache
        }

        return $this->render("frontend/projet_show.html.twig",[
            'projet' => $projet,
            'locale' => $_locale,
            'active' => 'projet'
        ]);
    }
}
