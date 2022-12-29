<?php

namespace App\Controller\Backend;

use App\Entity\EnJob;
use App\Form\EnJobType;
use App\Repository\EnJobRepository;
use App\Repository\FrJobRepository;
use App\Repository\TraductionRepository;
use App\Services\GestionCache;
use App\Services\GestionMedia;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/en/job')]
class BackendEnJobController extends AbstractController
{
    const LANG = 'en';
    const TRADUCTION_ENTITY = 'job';

    public function __construct(
        private Flasher $flasher, private Utility $utility, private GestionCache $gestionCache,
        private GestionMedia $gestionMedia, private FrJobRepository $frJobRepository,
        private TraductionRepository $traductionRepository
    )
    {
    }

    #[Route('/', name: 'app_backend_en_job_index', methods: ['GET'])]
    public function index(EnJobRepository $enJobRepository): Response
    {
        return $this->render('backend_en_job/index.html.twig', [
            'en_jobs' => $enJobRepository->findBy([],['id'=>"DESC"]),
        ]);
    }

    #[Route('/new', name: 'app_backend_en_job_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EnJobRepository $enJobRepository): Response
    {
        $enJob = new EnJob();
        $form = $this->createForm(EnJobType::class, $enJob);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verification de la version francaise
            $frjob = $this->frJobRepository->findOneBy(['id'=>$request->request->get('_traduction')]);
            if (!$frjob){
                $this->flasher
                    ->create('sweetalert')
                    ->icon('error')
                    ->addError("Warnin! please associate french version");

                return $this->redirectToRoute('app_backend_en_job_new',[], Response::HTTP_SEE_OTHER);
            }

            $this->utility->slug($enJob, 'enJob', true); // gestion des slugs et resume
            // gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'job');
                $enJob->setMedia($media);
            }
            $this->utility->referenceJob($enJob, self::LANG); // gestion de la reference

            $enJobRepository->save($enJob, true);

            $this->utility->traductionSave($frjob, $enJob, self::TRADUCTION_ENTITY);

            $this->gestionCache->cacheJob(self::LANG, true); // reinitialisation du cache

            $this->flasher
                ->create('notyf')
                ->addSuccess("Offer '{$enJob->getTitre()}' has been successfully added!");

            return $this->redirectToRoute('app_backend_en_job_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_job/new.html.twig', [
            'en_job' => $enJob,
            'form' => $form,
            'fr_jobs' => $this->frJobRepository->findListInactif(),
            'traduction' => null
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_job_show', methods: ['GET'])]
    public function show(EnJob $enJob): Response
    {
        return $this->render('backend_en_job/show.html.twig', [
            'en_job' => $enJob,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_en_job_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EnJob $enJob, EnJobRepository $enJobRepository): Response
    {
        $form = $this->createForm(EnJobType::class, $enJob);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { //dd($request->request->get('_traduction'));
            // Version francaise
            $frJob = $this->frJobRepository->findOneBy(['pageIndex'=>$request->request->get('_traduction')]);
            if (!$frJob){
                $this->flasher
                    ->create('sweetalert')
                    ->icon('error')
                    ->addError("Warning! please associate french correspondence");

                return  $this->redirectToRoute("app_backend_en_job_edit",['id'=>$enJob->getId()], Response::HTTP_SEE_OTHER);
            }

            $this->utility->slug($enJob, 'enJob', true); // Gestion des slug et resumé

            // Gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'job');
                if ($enJob->getMedia())
                    $this->gestionMedia->removeUpload($enJob->getMedia(), 'job');

                $enJob->setMedia($media);
            }

            $this->utility->referenceJob($enJob, self::LANG);
            $enJob->setSlug($enJob->getSlug().'-'.$enJob->getReference()); // Refactoration du slug

            $enJobRepository->save($enJob, true);

            $this->gestionCache->cacheJob(self::LANG, true); // gestion des caches

            $this->flasher
                ->create('notyf')
                ->addSuccess("Recruitment '{$enJob->getTitre()}' has been successfully updated!");

            return $this->redirectToRoute('app_backend_en_job_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_job/edit.html.twig', [
            'en_job' => $enJob,
            'form' => $form,
            'fr_jobs' => $this->frJobRepository->findAll(),
            'traduction' => $this->frJobRepository->findOneBy(['pageIndex' => $enJob->getPageIndex()])
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_job_delete', methods: ['POST'])]
    public function delete(Request $request, EnJob $enJob, EnJobRepository $enJobRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enJob->getId(), $request->request->get('_token'))) {
            $enJobRepository->remove($enJob, true);
            if ($enJob->getMedia())
                $this->gestionMedia->removeUpload($enJob->getMedia(), 'job');

            if ($enJob->getPageIndex())
                $this->utility->traductionDelete($enJob, 'job');

            $this->flasher
                ->create('notyf')
                ->addSuccess("Success! Recruitment '{$enJob->getTitre()}' has been successfully deleted:");


        }

        return $this->redirectToRoute('app_backend_en_job_index', [], Response::HTTP_SEE_OTHER);
    }
}
