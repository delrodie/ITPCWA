<?php

namespace App\Controller\Backend;

use App\Repository\CandidatRepository;
use App\Repository\EnJobRepository;
use App\Repository\FrJobRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/candidat')]
class BackendCandidatController extends AbstractController
{
    public function __construct(
        private CandidatRepository $candidatRepository, private EnJobRepository $enJobRepository,
        private FrJobRepository $frJobRepository
    )
    {
    }

    #[Route('/', name: 'app_backend_candidat_index')]
    public function index(): Response
    {
        return $this->render('frontend/candidats.html.twig',[
            'candidats' => $this->candidatRepository->findBy([],['createdAt' => "DESC"])
        ]);
    }

    #[Route('/{jobReference}', name: 'app_backend_candidat_show')]
    public function show($jobReference): Response
    {
        $fr = substr($jobReference, 0, 1);
        if ($fr === 'F') $job = $this->frJobRepository->findOneBy(['reference' => $jobReference]);
        else $job = $this->enJobRepository->findOneBy(['reference' => $jobReference]);

        return $this->render('frontend/candidat.html.twig',[
            'candidats' => $this->candidatRepository->findBy(['jobReference' => $jobReference]),
            'job' => $job
        ]);
    }
}
