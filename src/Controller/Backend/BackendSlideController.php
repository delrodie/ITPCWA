<?php

namespace App\Controller\Backend;

use App\Entity\Slide;
use App\Form\SlideType;
use App\Repository\SlideRepository;
use App\Services\GestionMedia;
use App\Services\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/slide')]
class BackendSlideController extends AbstractController
{
    public function __construct(
        private Utility $utility, private GestionMedia $gestionMedia,
    )
    {
    }

    #[Route('/', name: 'app_backend_slide_index', methods: ['GET','POST'])]
    public function index(Request $request, SlideRepository $slideRepository): Response
    {
        $slide = new Slide();
        $form = $this->createForm(SlideType::class, $slide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$this->utility->slug($slide, 'slide')){
                $this->addFlash('danger', "Attention il est existe déjà un slide de même titre");
                return $this->redirectToRoute('app_backend_slide_index',[], Response::HTTP_SEE_OTHER);
            }

            // Gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'slide');
                $slide->setMedia($media);
            }
            //dd($slide);
            $slideRepository->save($slide, true);

            $this->addFlash('success', "Le slide a été ajouté avec succès!");

            return $this->redirectToRoute('app_backend_slide_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_slide/index.html.twig', [
            'slides' => $slideRepository->findAll(),
            'slide' => $slide,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_backend_slide_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SlideRepository $slideRepository): Response
    {
        $slide = new Slide();
        $form = $this->createForm(SlideType::class, $slide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slideRepository->save($slide, true);

            return $this->redirectToRoute('app_backend_slide_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_slide/new.html.twig', [
            'slide' => $slide,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_slide_show', methods: ['GET'])]
    public function show(Slide $slide): Response
    {
        return $this->render('backend_slide/show.html.twig', [
            'slide' => $slide,
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_backend_slide_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Slide $slide, SlideRepository $slideRepository): Response
    {
        $form = $this->createForm(SlideType::class, $slide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($slide, 'slide'); //dd($slide);
            // Gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'slide');

                if ($slide->getMedia()) $this->gestionMedia->removeUpload($slide->getMedia(), 'slide');

                $slide->setMedia($media);
            }

            $slideRepository->save($slide, true);

            $this->addFlash('success', "Le slide a été modifié avec succès!");

            return $this->redirectToRoute('app_backend_slide_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_slide/edit.html.twig', [
            'slide' => $slide,
            'form' => $form,
            'slides' => $slideRepository->findAll()
        ]);
    }

    #[Route('/{id}', name: 'app_backend_slide_delete', methods: ['POST'])]
    public function delete(Request $request, Slide $slide, SlideRepository $slideRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$slide->getId(), $request->request->get('_token'))) {
            $slideRepository->remove($slide, true);

            if ($slide->getMedia()) $this->gestionMedia->removeUpload($slide->getMedia(), 'slide');
        }

        return $this->redirectToRoute('app_backend_slide_index', [], Response::HTTP_SEE_OTHER);
    }
}
