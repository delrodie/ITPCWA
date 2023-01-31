<?php

namespace App\Controller;

use App\Repository\CordonneeRepository;
use App\Services\GestionCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

#[Route('/coordonnee')]
class FrontendCordonneeController extends AbstractController
{
    #[Route('/', name: 'app_frontend_cordonnee_header')]
    public function header(GestionCache $cache): Response
    {
        return $this->render('frontend/coordonnee_header.html.twig',[
            'coordonnee' => $cache->cacheCoordonnee(),
        ]);
    }

    #[Route('/foot')]
    public function footer(GestionCache $cache): Response
    {
        return $this->render('frontend/coordonnee_footer.html.twig',[
            'coordonnee' => $cache->cacheCoordonnee(),
        ]);
    }
}
