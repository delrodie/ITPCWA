<?php

namespace App\Controller\Frontend;

use App\Services\GestionCache;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/multimedia')]
class FrontendMultimediaController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache, private PaginatorInterface $paginator
    )
    {
    }

    #[Route('/{_locale}/{rubrique}', name: 'app_frontend_multimedia', methods: ['GET','POST'])]
    public function index(Request $request, $_locale): Response
    {
        $datas = $this->gestionCache->cacheAlbum($_locale);
        $albums = $this->paginator->paginate(
            $datas,
            $request->query->getInt('page', 1),
            9
        );
        return $this->render('frontend/multimedias.html.twig',[
            'locale' => $_locale,
            'albums' => $albums,
            'active' => "galerie"
        ]);
    }

    #[Route('/{_locale}/{rubrique}/{slug}', name: 'app_frontend_multimedia_show', methods: ['GET','POST'])]
    public function show(Request $request, $_locale, $slug)
    {
        return $this->render('frontend/multimedia.html.twig',[
            'locale' => $_locale,
            'album' => $this->gestionCache->cacheAlbumItem($_locale, $slug),
            'active' => "galerie"
        ]);
    }
}
