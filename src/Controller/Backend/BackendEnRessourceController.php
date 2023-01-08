<?php

namespace App\Controller\Backend;

use App\Entity\EnRessource;
use App\Form\EnRessourceType;
use App\Repository\EnRessourceRepository;
use App\Repository\FrRessourceRepository;
use App\Services\GestionCache;
use App\Services\GestionMedia;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/en/ressource')]
class BackendEnRessourceController extends AbstractController
{
    public function __construct(
        private Flasher $flasher, private GestionMedia $gestionMedia, private Utility $utility,
        private GestionCache $gestionCache
    )
    {
    }

    #[Route('/', name: 'app_backend_en_ressource_index', methods: ['GET'])]
    public function index(EnRessourceRepository $enRessourceRepository): Response
    {
        return $this->render('backend_en_ressource/index.html.twig', [
            'en_ressources' => $enRessourceRepository->findBy([],['id'=>"DESC"]),
        ]);
    }

    #[Route('/new', name: 'app_backend_en_ressource_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EnRessourceRepository $enRessourceRepository): Response
    {
        $enRessource = new EnRessource();
        $form = $this->createForm(EnRessourceType::class, $enRessource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($enRessource, 'enRessource');
            // gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'ressource');
                $enRessource->setMedia($media);
            }
            $this->utility->getReference($enRessource, 'en');
            $enRessourceRepository->save($enRessource, true);

            $this->gestionCache->cacheRessource('en',true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("Resource {$enRessource->getTitre()} was successfully added!")
                ;

            return $this->redirectToRoute('app_backend_en_ressource_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_ressource/new.html.twig', [
            'en_ressource' => $enRessource,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_ressource_show', methods: ['GET'])]
    public function show(EnRessource $enRessource): Response
    {
        return $this->render('backend_en_ressource/show.html.twig', [
            'en_ressource' => $enRessource,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_en_ressource_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EnRessource $enRessource, EnRessourceRepository $enRessourceRepository): Response
    {
        $form = $this->createForm(EnRessourceType::class, $enRessource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($enRessource, 'enRessource'); // gestion des slugs
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'ressource');
                if ($enRessource->getMedia())
                    $this->gestionMedia->removeUpload($enRessource->getMedia(), 'ressource');

                $enRessource->setMedia($media);
            }

            $enRessourceRepository->save($enRessource, true);

            $this->gestionCache->cacheRessource('en',true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("Resource {$enRessource->getTitre()} was successfully updated!");

            return $this->redirectToRoute('app_backend_en_ressource_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_ressource/edit.html.twig', [
            'en_ressource' => $enRessource,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_ressource_delete', methods: ['POST'])]
    public function delete(Request $request, EnRessource $enRessource, EnRessourceRepository $enRessourceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enRessource->getId(), $request->request->get('_token'))) {
            $enRessourceRepository->remove($enRessource, true);
            if ($enRessource->getMedia())
                $this->gestionMedia->removeUpload($enRessource->getMedia(), 'ressource');

            $this->gestionCache->cacheRessource('en', true);

            $this->flasher
                ->create('sweetalert')
                ->icon('warning')
                ->addWarning("The corresponding resource must be deleted");

            $this->flasher
                ->create('notyf')
                ->addSuccess("Resource {$enRessource->getTitre()} was successfully added");
        }

        return $this->redirectToRoute('app_backend_fr_ressource_index', [], Response::HTTP_SEE_OTHER);
    }
}
