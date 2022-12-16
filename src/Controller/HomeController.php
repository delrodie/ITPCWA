<?php

namespace App\Controller;

use App\Repository\SlideRepository;
use App\Services\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private SlideRepository $slideRepository, private Utility $utility,
    )
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $this->utility->visiteur();

        return $this->render('home/index.html.twig', [
            'slides' => $this->slideRepository->findBy(['statut' => true], ['id'=>'DESC']),
        ]);
    }
}
