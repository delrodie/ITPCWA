<?php

namespace App\Controller;

use App\Repository\SlideRepository;
use App\Services\GestionCache;
use App\Services\Utility;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class HomeController extends AbstractController
{
    public function __construct(
        private SlideRepository $slideRepository, private Utility $utility, private GestionCache $gestionCache
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $this->utility->visiteur();


        return $this->render('home/index.html.twig', [
            'slides' => $this->gestionCache->cacheSlides(),
        ]);
    }
}
