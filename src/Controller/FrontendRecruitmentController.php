<?php

namespace App\Controller;

use App\Services\GestionCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/recruitment')]
class FrontendRecruitmentController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache
    )
    {
    }

    #[Route('/{_locale}', name: 'app_frontend_recruitment')]
    public function index($_locale): Response
    {

        return $this->render('frontend/recruitments.html.twig',[
            'locale' => $_locale,
            'recruitments' => $this->gestionCache->cacheJob($_locale, true),
            'pagination' => false
        ]);
    }

    #[Route('/{_locale}/{slug}', name: 'app_frontend_recruitment_show', methods: ['GET'])]
    public function show($_locale, $slug)
    {
        return $this->render('frontend/recruitment_show.html.twig',[
            'locale' => $_locale,
            'recruitment' => $this->gestionCache->cacheJobItem($_locale, $slug, true)
        ]);
    }
}
