<?php

namespace App\Controller\Backend;

use App\Entity\FrJob;
use App\Form\FrJobType;
use App\Repository\FrJobRepository;
use App\Services\GestionCache;
use App\Services\GestionMedia;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/fr/job')]
class BackendFrJobController extends AbstractController
{
    public function __construct(
        private Flasher $flasher, private Utility $utility, private GestionCache $gestionCache,
        private GestionMedia $gestionMedia
    )
    {
    }

    #[Route('/', name: 'app_backend_fr_job_index', methods: ['GET'])]
    public function index(FrJobRepository $frJobRepository): Response
    {
        return $this->render('backend_fr_job/index.html.twig', [
            'fr_jobs' => $frJobRepository->findBy([],['id'=>"DESC"]),
        ]);
    }

    #[Route('/new', name: 'app_backend_fr_job_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FrJobRepository $frJobRepository): Response
    {
        $frJob = new FrJob();
        $form = $this->createForm(FrJobType::class, $frJob);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($frJob, 'frJob', true); // gestion de slug et resume
            // gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'job');
                $frJob->setMedia($media);
            }

            $this->utility->referenceJob($frJob, 'fr'); // Generation de la reference
            $frJob->setSlug($frJob->getSlug().'-'.$frJob->getReference()); // refactoration du slug

            $frJobRepository->save($frJob, true);

            $this->gestionCache->cacheJob('fr', true);

            $this->flasher
                ->create('sweetalert')
                ->icon('warning')
                ->addWarning("Veuillez enregistrer la version anglaise de cette offre");

            $this->flasher
                ->create('notyf')
                ->addSuccess("L'offre '{$frJob->getTitre()} a été enregistrée avec succès!");

            return $this->redirectToRoute('app_backend_fr_job_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_job/new.html.twig', [
            'fr_job' => $frJob,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_job_show', methods: ['GET'])]
    public function show(FrJob $frJob): Response
    {
        return $this->render('backend_fr_job/show.html.twig', [
            'fr_job' => $frJob,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_fr_job_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FrJob $frJob, FrJobRepository $frJobRepository): Response
    {
        $form = $this->createForm(FrJobType::class, $frJob);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($frJob, 'frJob', true); // Gestion des slugs
            // gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'job');
                if ($frJob->getMedia())
                    $this->gestionMedia->removeUpload($frJob->getMedia(), 'job');

                $frJob->setMedia($media);
            }
            
            $frJob->setSlug($frJob->getSlug().'-'.$frJob->getReference()); // refactoration du slug

            $frJobRepository->save($frJob, true);

            $this->gestionCache->cacheJob('fr', true);

            $this->flasher
                ->create('sweetalert')
                ->icon('warning')
                ->addWarning("Veuillez modifier la version anglaise si possible");

            $this->flasher
                ->create('notyf')
                ->addSuccess("L'offre '{$frJob->getTitre()}' a été modifiée avec succès!");

            return $this->redirectToRoute('app_backend_fr_job_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_job/edit.html.twig', [
            'fr_job' => $frJob,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_job_delete', methods: ['POST'])]
    public function delete(Request $request, FrJob $frJob, FrJobRepository $frJobRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$frJob->getId(), $request->request->get('_token'))) {
            if ($frJob->getPageIndex()) {
                $this->flasher
                    ->create('sweetalert')
                    ->icon('warning')
                    ->addWarning("Cette offre '{$frJob->getTitre()}' est associée à une version anglaise donc veuillez supprimer d'abord la version anglaise");

                return $this->redirectToRoute('app_backend_en_job_index',[],Response::HTTP_SEE_OTHER);
            }

            $frJobRepository->remove($frJob, true);
            if ($frJob->getMedia())
                $this->gestionMedia->removeUpload($frJob->getMedia(), 'job');

            $this->flasher
                ->create('notyf')
                ->addSuccess("L'offre '{$frJob->getTitre()}' a été supprimée avec succès:");
        }

        return $this->redirectToRoute('app_backend_fr_job_index', [], Response::HTTP_SEE_OTHER);
    }
}
