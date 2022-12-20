<?php

namespace App\Controller;

use App\Entity\FrPresentation;
use App\Form\FrPresentationType;
use App\Repository\FrPresentationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/fr/presentation')]
class BackendFrPresentationController extends AbstractController
{
    #[Route('/', name: 'app_backend_fr_presentation_index', methods: ['GET'])]
    public function index(FrPresentationRepository $frPresentationRepository): Response
    {
        return $this->render('backend_fr_presentation/index.html.twig', [
            'fr_presentations' => $frPresentationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_backend_fr_presentation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FrPresentationRepository $frPresentationRepository): Response
    {
        $frPresentation = new FrPresentation();
        $form = $this->createForm(FrPresentationType::class, $frPresentation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $frPresentationRepository->save($frPresentation, true);

            return $this->redirectToRoute('app_backend_fr_presentation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_presentation/new.html.twig', [
            'fr_presentation' => $frPresentation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_presentation_show', methods: ['GET'])]
    public function show(FrPresentation $frPresentation): Response
    {
        return $this->render('backend_fr_presentation/show.html.twig', [
            'fr_presentation' => $frPresentation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_fr_presentation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FrPresentation $frPresentation, FrPresentationRepository $frPresentationRepository): Response
    {
        $form = $this->createForm(FrPresentationType::class, $frPresentation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $frPresentationRepository->save($frPresentation, true);

            return $this->redirectToRoute('app_backend_fr_presentation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_presentation/edit.html.twig', [
            'fr_presentation' => $frPresentation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_presentation_delete', methods: ['POST'])]
    public function delete(Request $request, FrPresentation $frPresentation, FrPresentationRepository $frPresentationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$frPresentation->getId(), $request->request->get('_token'))) {
            $frPresentationRepository->remove($frPresentation, true);
        }

        return $this->redirectToRoute('app_backend_fr_presentation_index', [], Response::HTTP_SEE_OTHER);
    }
}
