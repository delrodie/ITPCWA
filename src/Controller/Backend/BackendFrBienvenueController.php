<?php

namespace App\Controller\Backend;

use App\Entity\FrBienvenue;
use App\Form\FrBienvenueType;
use App\Repository\FrBienvenueRepository;
use App\Services\GestionCache;
use App\Services\GestionMedia;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/fr/bienvenue')]
class BackendFrBienvenueController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache, private GestionMedia $gestionMedia, private Utility $utility,
        private Flasher $flasher
    )
    {
    }

    #[Route('/', name: 'app_backend_fr_bienvenue_index', methods: ['GET'])]
    public function index(FrBienvenueRepository $frBienvenueRepository): Response
    {
        return $this->redirectToRoute('app_backend_fr_bienvenue_new');
    }

    #[Route('/new', name: 'app_backend_fr_bienvenue_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FrBienvenueRepository $frBienvenueRepository): Response
    {
        // Redirection de bienvenue
        if ($exist = $frBienvenueRepository->findOneBy([],['id'=>'DESC']))
            return $this->redirectToRoute('app_backend_fr_bienvenue_show', ['id' => $exist->getId()]);

        $frBienvenue = new FrBienvenue();
        $form = $this->createForm(FrBienvenueType::class, $frBienvenue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($frBienvenue, 'frBienvenue', true); // Gestion des slug
            // Gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'bienvenue');
                $frBienvenue->setMedia($media);
            }
            $frBienvenueRepository->save($frBienvenue, true);

            $this->gestionCache->cacheBienvenue('fr', true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("Le message de bienvenue a été enregistré avec succès!");

            return $this->redirectToRoute('app_backend_fr_bienvenue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_bienvenue/new.html.twig', [
            'fr_bienvenue' => $frBienvenue,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_bienvenue_show', methods: ['GET'])]
    public function show(FrBienvenue $frBienvenue): Response
    {
        return $this->render('backend_fr_bienvenue/show.html.twig', [
            'fr_bienvenue' => $frBienvenue,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_fr_bienvenue_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FrBienvenue $frBienvenue, FrBienvenueRepository $frBienvenueRepository): Response
    {
        $form = $this->createForm(FrBienvenueType::class, $frBienvenue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($frBienvenue, 'frBienvenue', true);
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'bienvenue');
                if ($frBienvenue->getMedia())
                    $this->gestionMedia->removeUpload($frBienvenue->getMedia(), 'bienvenue');
            }
            $frBienvenueRepository->save($frBienvenue, true);

            $this->gestionCache->cacheBienvenue('fr', true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("Le message de bienvenue a été modifié avec succès!");

            return $this->redirectToRoute('app_backend_fr_bienvenue_show', ['id'=>$frBienvenue->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_bienvenue/edit.html.twig', [
            'fr_bienvenue' => $frBienvenue,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_bienvenue_delete', methods: ['POST'])]
    public function delete(Request $request, FrBienvenue $frBienvenue, FrBienvenueRepository $frBienvenueRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$frBienvenue->getId(), $request->request->get('_token'))) {
            $frBienvenueRepository->remove($frBienvenue, true);
            if ($frBienvenue->getMedia())
                $this->gestionMedia->removeUpload($frBienvenue->getMedia(), 'bienvenue');

            $this->gestionCache->cacheBienvenue('fr', true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("Le message de bienvenue a été supprimé avec succès!");
        }

        return $this->redirectToRoute('app_backend_fr_bienvenue_index', [], Response::HTTP_SEE_OTHER);
    }
}
