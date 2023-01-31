<?php

namespace App\Controller\Frontend;

use App\Services\GestionCache;
use Flasher\Prime\Flasher;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/team')]
class FrontendEquipeController extends AbstractController
{
    public function __construct(
        Private GestionCache $gestionCache, private Flasher $flasher, private PaginatorInterface $paginator
    )
    {
    }

    #[Route('/{_locale}/', name: 'app_frontend_equipe_index')]
    public function index(Request $request, $_locale): Response
    {
        $datas = $this->gestionCache->cacheEquipe($_locale);
        $equipes = $this->paginator->paginate(
            $datas,
            $request->query->getInt('page', 1),
            9
        );
        return $this->render('frontend/equipes.html.twig',[
            'locale' => $_locale,
            'active' => "presentation",
            'equipes' => $equipes
        ]);
    }

    #[Route('/{_locale}/{slug}', name: 'app_frontend_equipe_show')]
    public function show($_locale, $slug)
    {
        return $this->render('frontend/equipe_membre.html.twig',[
            'locale' => $_locale,
            'active' => "presentation",
            'equipe' => $this->gestionCache->cacheEquipeItem($_locale, $slug)
        ]);
    }
}
