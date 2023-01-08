<?php

namespace App\Controller\Backend;

use App\Entity\FrRessource;
use App\Form\FrRessourceType;
use App\Repository\FrRessourceRepository;
use App\Services\GestionCache;
use App\Services\GestionMedia;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/fr/ressource')]
class BackendFrRessourceController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache, private GestionMedia $gestionMedia,
        private Utility $utility, private Flasher $flasher
    )
    {
    }

    #[Route('/', name: 'app_backend_fr_ressource_index', methods: ['GET'])]
    public function index(FrRessourceRepository $frRessourceRepository): Response
    {
        return $this->render('backend_fr_ressource/index.html.twig', [
            'fr_ressources' => $frRessourceRepository->findBy([],['id'=>"DESC"]),
        ]);
    }

    #[Route('/new', name: 'app_backend_fr_ressource_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FrRessourceRepository $frRessourceRepository): Response
    {
        $frRessource = new FrRessource();
        $form = $this->createForm(FrRessourceType::class, $frRessource);
        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->utility->getReference($frRessource, 'fr');
            $this->utility->slug($frRessource, 'frRessource'); // gestion des slugs
            // Gestion des fichiers
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $frRessource->setExtension($mediaFile->guessExtension()); // Recupération de l'extension
                $media = $this->gestionMedia->upload($mediaFile, 'ressource');
                $frRessource->setMedia($media);
            }

            $frRessourceRepository->save($frRessource, true);

            $this->gestionCache->cacheRessource('fr',true); // Gestion du cache

            $this->flasher
                ->create('sweetalert')
                ->addSuccess("Veuillez enregistrer la version anglaise");

            $this->flasher
                ->create('notyf')
                ->addSuccess("Ressource {$frRessource->getTitre()} a été enregistrée avec succès!");

            // Redirection form anglais

            return $this->redirectToRoute('app_backend_fr_ressource_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_ressource/new.html.twig', [
            'fr_ressource' => $frRessource,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_ressource_show', methods: ['GET'])]
    public function show(FrRessource $frRessource): Response
    {
        return $this->render('backend_fr_ressource/show.html.twig', [
            'fr_ressource' => $frRessource,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_fr_ressource_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FrRessource $frRessource, FrRessourceRepository $frRessourceRepository): Response
    {
        $form = $this->createForm(FrRessourceType::class, $frRessource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($frRessource, 'frRessource'); // Gestion des slugs
            // Gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $frRessource->setExtension($mediaFile->guessExtension());
                $media = $this->gestionMedia->upload($mediaFile, 'ressource');

                if ($frRessource->getMedia())
                    $this->gestionMedia->removeUpload($frRessource->getMedia(), 'ressource');

                $frRessource->setMedia($media);
            }

            $frRessourceRepository->save($frRessource, true);

            $this->gestionCache->cacheRessource('fr',true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("La ressource {$frRessource->getTitre()} a été modifiée avec succès!");

            return $this->redirectToRoute('app_backend_fr_ressource_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_ressource/edit.html.twig', [
            'fr_ressource' => $frRessource,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_ressource_delete', methods: ['POST'])]
    public function delete(Request $request, FrRessource $frRessource, FrRessourceRepository $frRessourceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$frRessource->getId(), $request->request->get('_token'))) {
            $frRessourceRepository->remove($frRessource, true);

            if ($frRessource->getMedia())
                $this->gestionMedia->removeUpload($frRessource->getMedia(), 'ressource');

            $this->gestionCache->cacheRessource('fr', true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("La ressource {$frRessource->getTitre()} a été supprimée avec succès!");
        }

        return $this->redirectToRoute('app_backend_fr_ressource_index', [], Response::HTTP_SEE_OTHER);
    }
}
