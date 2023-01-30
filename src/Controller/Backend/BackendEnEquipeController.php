<?php

namespace App\Controller\Backend;

use App\Entity\EnEquipe;
use App\Form\EnEquipeType;
use App\Repository\EnEquipeRepository;
use App\Repository\FrEquipeRepository;
use App\Services\GestionCache;
use App\Services\GestionMedia;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/en/equipe')]
class BackendEnEquipeController extends AbstractController
{
    public function __construct(
        private Utility $utility, private GestionMedia $gestionMedia, private Flasher $flasher,
        private FrEquipeRepository $frEquipeRepository, private GestionCache $gestionCache
    )
    {
    }

    #[Route('/', name: 'app_backend_en_equipe_index', methods: ['GET'])]
    public function index(EnEquipeRepository $enEquipeRepository): Response
    {
        return $this->render('backend_en_equipe/index.html.twig', [
            'en_equipes' => $enEquipeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_backend_en_equipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EnEquipeRepository $enEquipeRepository): Response
    {
        $enEquipe = new EnEquipe();
        $form = $this->createForm(EnEquipeType::class, $enEquipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Verification de l'association de la version française
            $frEquipe = $this->frEquipeRepository->findOneBy(['id' => $request->request->get('_traduction')]);
            if (!$frEquipe){
                $this->flasher->create('sweetalert')->addError("Warning! Please associate french version");

                return $this->redirectToRoute('app_backend_en_equipe_new',[], Response::HTTP_SEE_OTHER);
            }

            $this->utility->slugTeam($enEquipe, 'enEquipe'); // Gestion des slugs

            // Gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'equipe');
                $enEquipe->setMedia($media);
            }

            $enEquipeRepository->save($enEquipe, true);

            // Sauvegarde de la traduction
            $this->utility->traductionSave($frEquipe, $enEquipe, 'equipe');

            // Réinitialisation du cache
            $this->gestionCache->cacheEquipe('en', true);

            $this->flasher->create('notyf')->addSuccess("'{$enEquipe->getNom()}' member was successfully added!");

            return $this->redirectToRoute('app_backend_en_equipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_equipe/new.html.twig', [
            'en_equipe' => $enEquipe,
            'form' => $form,
            'fr_equipes' => $this->frEquipeRepository->findListInactif(),
            'traduction' => null
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_equipe_show', methods: ['GET'])]
    public function show(EnEquipe $enEquipe): Response
    {
        return $this->render('backend_en_equipe/show.html.twig', [
            'en_equipe' => $enEquipe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_en_equipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EnEquipe $enEquipe, EnEquipeRepository $enEquipeRepository): Response
    {
        $form = $this->createForm(EnEquipeType::class, $enEquipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Verification de l'association de la version française
            $frEquipe = $this->frEquipeRepository->findOneBy(['pageIndex' => $request->request->get('_traduction')]); //dd($frEquipe);
            if (!$frEquipe){
                $this->flasher->create('sweetalert')->addError("Warning! Please associate french version");

                return $this->redirectToRoute('app_backend_en_equipe_new',[], Response::HTTP_SEE_OTHER);
            }

            $this->utility->slugTeam($enEquipe, 'enEquipe'); // Gestion des slugs

            // Gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'equipe');
                if ($enEquipe->getMedia())
                    $this->gestionMedia->removeUpload($enEquipe->getMedia(), 'equipe');

                $enEquipe->setMedia($media);
            }

            $enEquipeRepository->save($enEquipe, true);

            // Reinitialisation du cache
            $this->gestionCache->cacheEquipeItem('en', $enEquipe->getSlug(), true);
            $this->gestionCache->cacheEquipe('en', true);

            $this->flasher->create('notyf')->addSuccess("'{$enEquipe->getNom()}' member was successfully updated!");

            return $this->redirectToRoute('app_backend_en_equipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_equipe/edit.html.twig', [
            'en_equipe' => $enEquipe,
            'form' => $form,
            'fr_equipes' => $this->frEquipeRepository->findAll(),
            'traduction' => $this->frEquipeRepository->findOneBy(['pageIndex' => $enEquipe->getPageIndex()])
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_equipe_delete', methods: ['POST'])]
    public function delete(Request $request, EnEquipe $enEquipe, EnEquipeRepository $enEquipeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enEquipe->getId(), $request->request->get('_token'))) {
            $enEquipeRepository->remove($enEquipe, true);

            if ($enEquipe->getMedia())
                $this->gestionMedia->removeUpload($enEquipe->getMedia(), 'equipe');

            if ($enEquipe->getPageIndex())
                $this->utility->traductionDelete($enEquipe, 'equipe');

            // Réinitialisation du cache
            $this->gestionCache->cacheEquipe('en', true);
        }

        return $this->redirectToRoute('app_backend_en_equipe_index', [], Response::HTTP_SEE_OTHER);
    }
}
