<?php

namespace App\Controller\Backend;

use App\Services\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/log')]
class BackendLogController extends AbstractController
{
    public function __construct(private Utility $utility)
    {

    }
    #[Route('/', name: 'app_backend_log_visiteur')]
    public function index(): Response
    {
        return $this->render('backend/logs.html.twig',[
            'pages' => $this->utility->pagePlusVisited()
        ]);
    }
}
