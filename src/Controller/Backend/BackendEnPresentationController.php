<?php

namespace App\Controller\Backend;

use App\Entity\EnPresentation;
use App\Form\EnPresentationType;
use App\Repository\EnPresentationRepository;
use App\Services\GestionCache;
use App\Services\GestionMedia;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/en/presentation')]
class BackendEnPresentationController extends AbstractController
{
    public function __construct(
        private Utility $utility, private GestionMedia $gestionMedia, private GestionCache $gestionCache,
        private Flasher $flasher
    )
    {
    }

    #[Route('/', name: 'app_backend_en_presentation_index', methods: ['GET'])]
    public function index(EnPresentationRepository $enPresentationRepository): Response
    {
        return $this->render('backend_en_presentation/index.html.twig', [
            'en_presentations' => $enPresentationRepository->findAll(),
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/new', name: 'app_backend_en_presentation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EnPresentationRepository $enPresentationRepository): Response
    {
        $enPresentation = new EnPresentation();
        $form = $this->createForm(EnPresentationType::class, $enPresentation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verification de l'utilisation unique de la rubrique
            $verif = $enPresentationRepository->findOneBy(['type' => $enPresentation->getType()->getId()]);
            if ($verif){
                $this->flasher
                    ->create('sweetalert')
                    ->addError("Please note, category '{$enPresentation->getType()->getTitre()}' has already been associated with an article!");

                return $this->redirectToRoute('app_backend_en_presentation_new',[],Response::HTTP_SEE_OTHER);
            }

            $this->utility->slug($enPresentation, 'enPresentation', true); // Gestion du slug et du resumé
            // Gestion des médias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'presentation');
                $enPresentation->setMedia($media);
            }

            $enPresentationRepository->save($enPresentation, true);

            $this->gestionCache->cacheEnPresentation($enPresentation->getType()->getSlug(), true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("Item '{$enPresentation->getTitre()}' has been successfully added!");

            return $this->redirectToRoute('app_backend_en_presentation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_presentation/new.html.twig', [
            'en_presentation' => $enPresentation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_presentation_show', methods: ['GET'])]
    public function show(EnPresentation $enPresentation): Response
    {
        return $this->render('backend_en_presentation/show.html.twig', [
            'en_presentation' => $enPresentation,
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}/edit', name: 'app_backend_en_presentation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EnPresentation $enPresentation, EnPresentationRepository $enPresentationRepository): Response
    {
        $form = $this->createForm(EnPresentationType::class, $enPresentation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($enPresentation, 'enPresentation', true); // Gestion des slugs et du résumé
            //Gestion des médias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'presentation');
                if ($enPresentation->getMedia())
                    $this->gestionMedia->removeUpload($enPresentation->getMedia(), 'presentation');

                $enPresentation->setMedia($media);
            }
            $enPresentationRepository->save($enPresentation, true);

            $this->gestionCache->cacheEnPresentation($enPresentation->getType()->getSlug(), true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("Item '{$enPresentation->getTitre()}' has been successfully updated!");

            return $this->redirectToRoute('app_backend_en_presentation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_presentation/edit.html.twig', [
            'en_presentation' => $enPresentation,
            'form' => $form,
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'app_backend_en_presentation_delete', methods: ['POST'])]
    public function delete(Request $request, EnPresentation $enPresentation, EnPresentationRepository $enPresentationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enPresentation->getId(), $request->request->get('_token'))) {
            $enPresentationRepository->remove($enPresentation, true);
            if ($enPresentation->getMedia()){
                $this->gestionMedia->removeUpload($enPresentation->getMedia(), 'presentation');
            }

            $this->gestionCache->cacheEnPresentation($enPresentation->getType()->getSlug(), true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("Item '{$enPresentation->getTitre()} has been successfully deleted:");
        }

        return $this->redirectToRoute('app_backend_en_presentation_index', [], Response::HTTP_SEE_OTHER);
    }
}
