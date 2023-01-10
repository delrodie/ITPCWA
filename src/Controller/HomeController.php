<?php

namespace App\Controller;

use App\Repository\MaintenanceRepository;
use App\Repository\SlideRepository;
use App\Services\GestionCache;
use App\Services\Utility;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class HomeController extends AbstractController
{
    public function __construct(
        private MaintenanceRepository $maintenanceRepository
    )
    {
    }


    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        return $this->redirectToRoute('app_frontend_index',['_locale' => $request->getLocale()], Response::HTTP_SEE_OTHER);

    }
}
