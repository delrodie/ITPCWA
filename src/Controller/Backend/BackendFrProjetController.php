<?php

namespace App\Controller\Backend;

use App\Entity\FrProjet;
use App\Form\FrProjetType;
use App\Repository\FrProjetRepository;
use App\Services\GestionCache;
use App\Services\GestionMedia;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/fr/projet')]
class BackendFrProjetController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache, private Utility $utility, private Flasher $flasher,
        private GestionMedia $gestionMedia
    )
    {
    }

    #[Route('/', name: 'app_backend_fr_projet_index', methods: ['GET'])]
    public function index(FrProjetRepository $frProjetRepository): Response
    {
        return $this->render('backend_fr_projet/index.html.twig', [
            'fr_projets' => $frProjetRepository->findBy([],['id'=>"DESC"]),
        ]);
    }

    #[Route('/new', name: 'app_backend_fr_projet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FrProjetRepository $frProjetRepository): Response
    {
        $frProjet = new FrProjet();
        $form = $this->createForm(FrProjetType::class, $frProjet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($frProjet, 'frProjet', true); // Gestion des slug
            // Gestion des medias
            $mediaFile = $form-> get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'projet');
                $frProjet->setMedia($media);
            }
            $frProjetRepository->save($frProjet, true);

            $this->gestionCache->cacheFrProjet(true);

            $this->flasher
                ->create('sweetalert')
                ->icon('warning')
                ->addSuccess("Veuillez enregistrer la version anglaise de ce projet");

            $this->flasher
                ->create('notyf')
                ->addSuccess("Le projet {$frProjet->getTitre()} a été ajouté avec succès!");

            return $this->redirectToRoute('app_backend_fr_projet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_projet/new.html.twig', [
            'fr_projet' => $frProjet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_projet_show', methods: ['GET'])]
    public function show(FrProjet $frProjet): Response
    {
        return $this->render('backend_fr_projet/show.html.twig', [
            'fr_projet' => $frProjet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_fr_projet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FrProjet $frProjet, FrProjetRepository $frProjetRepository): Response
    {
        $form = $this->createForm(FrProjetType::class, $frProjet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($frProjet, 'frProjet', true);
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'projet');
                if ($frProjet->getMedia())
                    $this->gestionMedia->removeUpload($frProjet->getMedia, 'projet');

                $frProjet->setMedia($media);
            }

            $frProjetRepository->save($frProjet, true);

            $this->gestionCache->cacheFrProjet(true); // Suppression du cache

            $this->flasher
                ->create('sweetalert')
                ->addSuccess("Attention veuillez modifier la version anglaise correspondance afin de l'activer");

            $this->flasher
                ->create('notyf')
                ->addSuccess("Le projet '{$frProjet->getTitre()}' a été modifié avec succès!");

            return $this->redirectToRoute('app_backend_fr_projet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_projet/edit.html.twig', [
            'fr_projet' => $frProjet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_projet_delete', methods: ['POST'])]
    public function delete(Request $request, FrProjet $frProjet, FrProjetRepository $frProjetRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$frProjet->getId(), $request->request->get('_token'))) {
            if ($frProjet->getPageIndex()){
                $this->flasher
                    ->create('sweetalert')
                    ->icon('error')
                    ->addError("Attention, la version anglaise correspondante devrait être supprimée d'abord!");

                // Redirection a la version anglaise
                dd('redirection a la version anglaise');
            }

            $frProjetRepository->remove($frProjet, true);

            $this->gestionCache->cacheFrProjet(true); // suppression du cache

            if ($frProjet->getMedia())
                $this->gestionMedia->removeUpload($frProjet->getMedia());

            $this->flasher
                ->create('notyf')
                ->addSuccess("Le projet '{$frProjet->getTitre()}' a été supprimé avec succès!");
        }

        return $this->redirectToRoute('app_backend_fr_projet_index', [], Response::HTTP_SEE_OTHER);
    }
}
