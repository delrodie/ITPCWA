<?php

namespace App\Controller\Backend;

use App\Entity\FrActualite;
use App\Form\FrActualiteType;
use App\Repository\FrActualiteRepository;
use App\Services\GestionCache;
use App\Services\GestionMedia;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/fr/actualite')]
class BackendFrActualiteController extends AbstractController
{
    public function __construct(
        private Flasher $flasher, private GestionCache $gestionCache, private GestionMedia $gestionMedia,
        private Utility $utility
    )
    {
    }

    #[Route('/', name: 'app_backend_fr_actualite_index', methods: ['GET'])]
    public function index(FrActualiteRepository $frActualiteRepository): Response
    {
        return $this->render('backend_fr_actualite/index.html.twig', [
            'fr_actualites' => $frActualiteRepository->findBy([],['id'=>"DESC"]),
        ]);
    }

    #[Route('/new', name: 'app_backend_fr_actualite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FrActualiteRepository $frActualiteRepository): Response
    {
        $frActualite = new FrActualite();
        $form = $this->createForm(FrActualiteType::class, $frActualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($frActualite, 'frActualite', true); // Gestion des slugs
            // Gestions des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'actualite');
                $frActualite->setMedia($media);
            }
            $frActualiteRepository->save($frActualite, true);

            $this->flasher
                ->create('sweetalert')
                ->icon('warning')
                ->addWarning("Veuillez enregistrer la version anglaise de cet article");

            $this->flasher
                ->create('notyf')
                ->addSuccess("L'article '{$frActualite->getTitre()} a été ajouté avec succès!");

            return $this->redirectToRoute('app_backend_fr_actualite_index', [], Response::HTTP_SEE_OTHER);
        }

        $this->flasher
            ->addWarning("Attention vous serez rediriger directement sur le formulaire pour la version anglaise");

        return $this->renderForm('backend_fr_actualite/new.html.twig', [
            'fr_actualite' => $frActualite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_actualite_show', methods: ['GET'])]
    public function show(FrActualite $frActualite): Response
    {
        return $this->render('backend_fr_actualite/show.html.twig', [
            'fr_actualite' => $frActualite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_fr_actualite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FrActualite $frActualite, FrActualiteRepository $frActualiteRepository): Response
    {
        $form = $this->createForm(FrActualiteType::class, $frActualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($frActualite, 'frActualite', true);
            // Gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'actualite');

                if ($frActualite->getMedia())
                    $this->gestionMedia->removeUpload($frActualite->getMedia(), 'actualite');

                $frActualite->setMedia($media);
            }

            $frActualiteRepository->save($frActualite, true);

            $this->flasher
                ->create('sweetalert')
                ->icon('warning')
                ->addWarning("Veuillez modifier la version anglaise correspondante à cette article");

            $this->flasher
                ->create('notyf')
                ->addSuccess("L'article de l'actualité '{$frActualite->getTitre()}' a été modifié avec succès!");

            return $this->redirectToRoute('app_backend_fr_actualite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_actualite/edit.html.twig', [
            'fr_actualite' => $frActualite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_actualite_delete', methods: ['POST'])]
    public function delete(Request $request, FrActualite $frActualite, FrActualiteRepository $frActualiteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$frActualite->getId(), $request->request->get('_token'))) {
            $frActualiteRepository->remove($frActualite, true);
        }

        return $this->redirectToRoute('app_backend_fr_actualite_index', [], Response::HTTP_SEE_OTHER);
    }
}
