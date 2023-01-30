<?php

namespace App\Controller\Backend;

use App\Entity\FrEquipe;
use App\Form\FrEquipeType;
use App\Repository\FrEquipeRepository;
use App\Services\GestionMedia;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/fr/equipe')]
class BackendFrEquipeController extends AbstractController
{
    public function __construct(
        private GestionMedia $gestionMedia, private Utility $utility, private Flasher $flasher
    )
    {
    }

    #[Route('/', name: 'app_backend_fr_equipe_index', methods: ['GET'])]
    public function index(FrEquipeRepository $frEquipeRepository): Response
    {
        return $this->render('backend_fr_equipe/index.html.twig', [
            'fr_equipes' => $frEquipeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_backend_fr_equipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FrEquipeRepository $frEquipeRepository): Response
    {
        $frEquipe = new FrEquipe();
        $form = $this->createForm(FrEquipeType::class, $frEquipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slugTeam($frEquipe, 'frEquipe'); // Gestion des slugs
            // Gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'equipe');
                $frEquipe->setMedia($media);
            }

            $frEquipeRepository->save($frEquipe, true);

            $this->flasher->create('notyf')->addSuccess("Le membre '{$frEquipe->getNom()} {$frEquipe->getPrenom()}' a été ajouté avec succès!");

            return $this->redirectToRoute('app_backend_fr_equipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_equipe/new.html.twig', [
            'fr_equipe' => $frEquipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_equipe_show', methods: ['GET'])]
    public function show(FrEquipe $frEquipe): Response
    {
        return $this->render('backend_fr_equipe/show.html.twig', [
            'fr_equipe' => $frEquipe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_fr_equipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FrEquipe $frEquipe, FrEquipeRepository $frEquipeRepository): Response
    {
        $form = $this->createForm(FrEquipeType::class, $frEquipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slugTeam($frEquipe, 'frEquipe'); // GEstion des slugs
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'equipe');
                if ($frEquipe->getMedia())
                    $this->gestionMedia->removeUpload($frEquipe->getMedia(), 'equipe');

                $frEquipe->setMedia($media);
            }

            $frEquipeRepository->save($frEquipe, true);

            $this->flasher->create('notyf')->addSuccess("Le membre '{$frEquipe->getNom()}' a été modifié avec succès!");

            return $this->redirectToRoute('app_backend_fr_equipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_equipe/edit.html.twig', [
            'fr_equipe' => $frEquipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_equipe_delete', methods: ['POST'])]
    public function delete(Request $request, FrEquipe $frEquipe, FrEquipeRepository $frEquipeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$frEquipe->getId(), $request->request->get('_token'))) {
            $frEquipeRepository->remove($frEquipe, true);
            if ($frEquipe->getMedia())
                $this->gestionMedia->removeUpload($frEquipe->getMedia(), 'equipe');

            $this->flasher->create('notyf')->addSuccess("Le memebre '{$frEquipe->getPrenom()}' a été modifié avec succès!");
        }

        return $this->redirectToRoute('app_backend_fr_equipe_index', [], Response::HTTP_SEE_OTHER);
    }
}
