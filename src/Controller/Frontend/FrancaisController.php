<?php

namespace App\Controller\Frontend;

use App\Services\AllRepository;
use App\Services\GestionCache;
use App\Services\Utility;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/fr')]
class FrancaisController extends AbstractController
{
    public function __construct(
        private Utility $utility, private GestionCache $gestionCache, private AllRepository $allRepository
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/', name: 'app_francais')]
    public function index(): Response
    {
        $this->utility->visiteur();
        return $this->render('francais/index.html.twig',[
            'slides' => $this->gestionCache->cacheSlides()
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/message', name: 'app_francais_message')]
    public function message(): Response
    {
        return $this->render('francais/message.html.twig',[
            'messages' => $this->gestionCache->cacheFrMessages(),
            //'messages' => $this->allRepository->frMessageActif()
        ]);
    }
}
