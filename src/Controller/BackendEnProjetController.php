<?php

namespace App\Controller;

use App\Entity\EnProjet;
use App\Form\EnProjetType;
use App\Repository\EnProjetRepository;
use App\Repository\FrProjetRepository;
use App\Services\GestionCache;
use App\Services\GestionMedia;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/en/projet')]
class BackendEnProjetController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache, private GestionMedia $gestionMedia, private Utility $utility,
        private Flasher $flasher, private FrProjetRepository $frProjetRepository
    )
    {
    }

    #[Route('/', name: 'app_backend_en_projet_index', methods: ['GET'])]
    public function index(EnProjetRepository $enProjetRepository): Response
    {
        return $this->render('backend_en_projet/index.html.twig', [
            'en_projets' => $enProjetRepository->findBy([],['id'=>"DESC"]),
        ]);
    }

    #[Route('/new', name: 'app_backend_en_projet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EnProjetRepository $enProjetRepository): Response
    {
        $enProjet = new EnProjet();
        $form = $this->createForm(EnProjetType::class, $enProjet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verification de l'existence de la version francaise
            $frProjet = $this->frProjetRepository->findOneBy(['id' => $request->request->get('_traduction')]);
            if (!$frProjet){
                $this->flasher
                    ->create('sweetalert')
                    ->icon('error')
                    ->addError("Warning! Please associate the french version.");

                return $this->redirectToRoute('app_backend_en_projet_new',[], Response::HTTP_SEE_OTHER);
            }

            $this->utility->slug($enProjet, 'enProjet', true);
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'projet');
                $enProjet->setMedia($media);
            }

            $enProjetRepository->save($enProjet, true);

            $this->utility->traductionSave($frProjet, $enProjet, 'projet');

            $this->gestionCache->cacheEnProjet(true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("Project '{$enProjet->getTitre()}' added successfully!");
                ;

            return $this->redirectToRoute('app_backend_en_projet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_projet/new.html.twig', [
            'en_projet' => $enProjet,
            'form' => $form,
            'fr_projets' => $this->frProjetRepository->findListInactif(),
            'traduction' => null,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_projet_show', methods: ['GET'])]
    public function show(EnProjet $enProjet): Response
    {
        return $this->render('backend_en_projet/show.html.twig', [
            'en_projet' => $enProjet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_en_projet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EnProjet $enProjet, EnProjetRepository $enProjetRepository): Response
    {
        $form = $this->createForm(EnProjetType::class, $enProjet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($enProjet, 'enProjet', true);
            // Gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'projet');

                if ($enProjet->getMedia())
                    $this->gestionMedia->removeUpload($enProjet->getMedia(), 'projet');

                $enProjet->setMedia();
            }

            $enProjetRepository->save($enProjet, true);

            $this->gestionCache->cacheEnProjet(true); // gestion des caches

            $this->flasher
                ->create('notyf')
                ->addSuccess("Project {$enProjet->getTitre()} updated successfully!");

            return $this->redirectToRoute('app_backend_en_projet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_projet/edit.html.twig', [
            'en_projet' => $enProjet,
            'form' => $form,
            'traduction' => $this->frProjetRepository->findOneBy(['pageIndex' => $enProjet->getPageIndex()]),
            'fr_projets' => $this->frProjetRepository->findAll()
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_projet_delete', methods: ['POST'])]
    public function delete(Request $request, EnProjet $enProjet, EnProjetRepository $enProjetRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enProjet->getId(), $request->request->get('_token'))) {
            //dd($enProjet);
            $enProjetRepository->remove($enProjet, true);

            if ($enProjet->getMedia())
                $this->gestionMedia->removeUpload($enProjet->getMedia(), 'projet');

            $this->utility->traductionDelete($enProjet, 'projet');

            $this->flasher
                ->create('notyf')
                ->addSuccess("Project {$enProjet->getTitre()} deleted successfylly!");

            $traduction = $this->frProjetRepository->findOneBy(['pageIndex'=>$enProjet->getPageIndex()]);

            $this->gestionCache->cacheEnProjet(true);

            if ($traduction){
                $this->flasher
                    ->create('sweetalert')
                    ->addSuccess("We invite you to delete the French correspondence");

                return $this->redirectToRoute('app_backend_fr_projet_show',['id' => $traduction->getId()], Response::HTTP_SEE_OTHER);
            }


        }

        return $this->redirectToRoute('app_backend_en_projet_index', [], Response::HTTP_SEE_OTHER);
    }
}
