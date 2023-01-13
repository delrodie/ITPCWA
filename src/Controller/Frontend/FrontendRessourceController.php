<?php

namespace App\Controller\Frontend;

use App\Services\GestionCache;
use App\Services\Utility;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ressource')]
class FrontendRessourceController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache, private Utility $utility, private PaginatorInterface $paginator
    )
    {
    }

    #[Route('/{_locale}', name: 'app_frontend_ressource')]
    public function index(Request $request, $_locale): Response
    {
        $datas = $this->gestionCache->cacheRessource($_locale);
        $ressources = $this->paginator->paginate(
            $datas,
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('frontend/ressources.html.twig',[
            'ressources' => $ressources,
            'locale' => $_locale,
            'pagination' => false,
            'active' => "ressource"
        ]);
    }
}
