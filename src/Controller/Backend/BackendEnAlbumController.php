<?php

namespace App\Controller\Backend;

use App\Entity\EnAlbum;
use App\Form\EnAlbumType;
use App\Repository\AlbumRepository;
use App\Repository\EnAlbumRepository;
use App\Repository\TraductionRepository;
use App\Services\GestionCache;
use App\Services\GestionMedia;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/en/album')]
class BackendEnAlbumController extends AbstractController
{
    const TRADUCTION_ENTITY = 'album';

    public function __construct(
        private GestionMedia $gestionMedia, private GestionCache $gestionCache, private Utility $utility,
        private Flasher $flasher, private AlbumRepository $frAlbumRepository, private TraductionRepository $traductionRepository
    )
    {
    }

    #[Route('/', name: 'app_backend_en_album_index', methods: ['GET'])]
    public function index(EnAlbumRepository $enAlbumRepository): Response
    {
        return $this->render('backend_en_album/index.html.twig', [
            'albums' => $enAlbumRepository->findBy([],['id'=>'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_backend_en_album_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EnAlbumRepository $enAlbumRepository): Response
    {
        $enAlbum = new EnAlbum();
        $form = $this->createForm(EnAlbumType::class, $enAlbum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verification de l'existence de la version francaise
            $frAlbum = $this->frAlbumRepository->findOneBy(['id'=>$request->request->get('_traduction')]);
            if (!$frAlbum){
                $this->flasher
                    ->create('sweetalert')
                    ->icon('error')
                    ->addError("Warning! associate the french version!");
            }
            $this->utility->slug($enAlbum, 'enAlbum'); /// Gestion des slugs
            // Gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'multimedia');
                $enAlbum->setMedia($media);
            }

            $enAlbumRepository->save($enAlbum, true);

            $this->utility->traductionSave($frAlbum, $enAlbum, 'album');

            $this->gestionCache->cacheAlbum('en', true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("Album '{$enAlbum->getTitre()}' has been successfully added!");

            return $this->redirectToRoute('app_backend_en_album_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_album/new.html.twig', [
            'album' => $enAlbum,
            'form' => $form,
            'fr_albums' => $this->frAlbumRepository->findListInactif(),
            'traduction' => null
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_album_show', methods: ['GET'])]
    public function show(EnAlbum $enAlbum): Response
    {
        return $this->render('backend_en_album/show.html.twig', [
            'en_album' => $enAlbum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_en_album_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EnAlbum $enAlbum, EnAlbumRepository $enAlbumRepository): Response
    {
        $form = $this->createForm(EnAlbumType::class, $enAlbum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // gestion de la correspondance
            if (!$request->request->get('_traduction')){
                $this->flasher
                    ->create('sweetalert')
                    ->icon('error')
                    ->addError("French version is mandatory");

                return $this->redirectToRoute("app_backend_en_album_edit",['id'=>$enAlbum->getId()], Response::HTTP_SEE_OTHER);
            }

            $this->utility->slug($enAlbum, 'enAlbum'); // Gestion des slugs
            // Gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'multimedia');
                if ($enAlbum->getMedia())
                    $this->gestionMedia->removeUpload($enAlbum->getMedia(), 'multimedia');

                $enAlbum->setMedia($media);
            }

            $enAlbumRepository->save($enAlbum, true);

            $this->gestionCache->cacheAlbum('en', true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("Album '{$enAlbum->getTitre()}' has been successfully updated!");

            return $this->redirectToRoute('app_backend_en_album_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_album/edit.html.twig', [
            'album' => $enAlbum,
            'form' => $form,
            'fr_albums' => $this->frAlbumRepository->findListInactif(),
            'traduction' => $this->utility->traductionSelect($enAlbum->getPageIndex(), self::TRADUCTION_ENTITY)
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_album_delete', methods: ['POST'])]
    public function delete(Request $request, EnAlbum $enAlbum, EnAlbumRepository $enAlbumRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enAlbum->getId(), $request->request->get('_token'))) {
            $enAlbumRepository->remove($enAlbum, true);
            if ($enAlbum->getMedia())
                $this->gestionMedia->removeUpload($enAlbum->getMedia(), 'multimedia');

            if ($enAlbum->getPageIndex())
                $this->utility->traductionDelete($enAlbum, 'album');

            $this->gestionCache->cacheAlbum('en', true);
        }

        return $this->redirectToRoute('app_backend_en_album_index', [], Response::HTTP_SEE_OTHER);
    }
}
