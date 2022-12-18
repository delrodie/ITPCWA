<?php

namespace App\Controller;

use App\Services\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private Utility $utility,
    )
    {
    }

    #[Route('/dashboard', name: 'app_backend_dashboard')]
    public function index(): Response
    {

        return $this->render('backend/dashboard.html.twig',[
            'visiteurs' => $this->utility->vistiteurList(),
            'pages' => $this->utility->pagePlusVisited(),
            'logs' => $this->utility->visteurLogs(),
        ]);
    }
}
