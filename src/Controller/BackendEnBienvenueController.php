<?php

namespace App\Controller;

use App\Entity\EnBienvenue;
use App\Form\EnBienvenueType;
use App\Repository\EnBienvenueRepository;
use App\Services\GestionCache;
use App\Services\GestionMedia;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/en/bienvenue')]
class BackendEnBienvenueController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache, private GestionMedia $gestionMedia, private Utility $utility,
        private Flasher $flasher
    )
    {
    }

    #[Route('/', name: 'app_backend_en_bienvenue_index', methods: ['GET'])]
    public function index(EnBienvenueRepository $enBienvenueRepository): Response
    {
        return $this->redirectToRoute('app_backend_en_bienvenue_new');
    }

    #[Route('/new', name: 'app_backend_en_bienvenue_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EnBienvenueRepository $enBienvenueRepository): Response
    {
        // Verification d'existence
        if ($exist = $enBienvenueRepository->findOneBy([],['id'=>"DESC"]))
            return $this->redirectToRoute('app_backend_en_bienvenue_show',['id'=>$exist->getId()]);

        $enBienvenue = new EnBienvenue();
        $form = $this->createForm(EnBienvenueType::class, $enBienvenue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($enBienvenue, 'enBienvenue', true);
            // Gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'bienvenue');
                $enBienvenue->setMedia($media);
            }

            $enBienvenueRepository->save($enBienvenue, true);

            $this->gestionCache->cacheBienvenue('en', true); // Gestion des caches

            $this->flasher
                ->create('notyf')
                ->addSuccess("Welcome message has been successfully added!");

            return $this->redirectToRoute('app_backend_en_bienvenue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_bienvenue/new.html.twig', [
            'en_bienvenue' => $enBienvenue,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_bienvenue_show', methods: ['GET'])]
    public function show(EnBienvenue $enBienvenue): Response
    {
        return $this->render('backend_en_bienvenue/show.html.twig', [
            'en_bienvenue' => $enBienvenue,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_en_bienvenue_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EnBienvenue $enBienvenue, EnBienvenueRepository $enBienvenueRepository): Response
    {
        $form = $this->createForm(EnBienvenueType::class, $enBienvenue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($enBienvenue, 'enBienvenue', true); // Gestion des caches

            //Gestion des medias
            if ($mediaFile = $form->get('media')->getData()){
                $media = $this->gestionMedia->upload($mediaFile, 'bienvenue');
                if ($enBienvenue->getMedia())
                    $this->gestionMedia->removeUpload($enBienvenue->getMedia(), 'bienvenue');

                $enBienvenue->setMedia($media);
            }

            $enBienvenueRepository->save($enBienvenue, true);

            $this->gestionCache->cacheBienvenue('en', true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("Welcome message has been successfully updated");

            return $this->redirectToRoute('app_backend_en_bienvenue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_bienvenue/edit.html.twig', [
            'en_bienvenue' => $enBienvenue,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_bienvenue_delete', methods: ['POST'])]
    public function delete(Request $request, EnBienvenue $enBienvenue, EnBienvenueRepository $enBienvenueRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enBienvenue->getId(), $request->request->get('_token'))) {
            $enBienvenueRepository->remove($enBienvenue, true);

            if ($enBienvenue->getMedia())
                $this->gestionMedia->removeUpload($enBienvenue->getMedia(), true);

            $this->gestionCache->cacheBienvenue('en', true);

            $this->flasher
                ->create("notyf")
                ->addSuccess('Welcome message has been successfully deleted');
        }

        return $this->redirectToRoute('app_backend_en_bienvenue_index', [], Response::HTTP_SEE_OTHER);
    }
}
