<?php

namespace App\Controller\Backend;

use App\Entity\Album;
use App\Form\AlbumType;
use App\Repository\AlbumRepository;
use App\Services\GestionCache;
use App\Services\GestionMedia;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/album')]
class BackendAlbumController extends AbstractController
{
    public function __construct(
        private Utility $utility, private GestionMedia $gestionMedia, private GestionCache $gestionCache,
        private Flasher $flasher
    )
    {
    }

    #[Route('/', name: 'app_backend_album_index', methods: ['GET'])]
    public function index(AlbumRepository $albumRepository): Response
    {
        return $this->render('backend_album/index.html.twig', [
            'albums' => $albumRepository->findBy([],['id'=>'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_backend_album_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AlbumRepository $albumRepository): Response
    {
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($album, 'album'); // Gestion des slugs
            // Gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'multimedia');
                $album->setMedia($media);
            }
            $albumRepository->save($album, true);

            $this->gestionCache->cacheAlbum('fr', true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("L'album {$album->getTitre()} a été ajouté avec succès!");

            return $this->redirectToRoute('app_backend_album_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_album/new.html.twig', [
            'album' => $album,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_album_show', methods: ['GET'])]
    public function show(Album $album): Response
    {
        return $this->render('backend_album/show.html.twig', [
            'album' => $album,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_album_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Album $album, AlbumRepository $albumRepository): Response
    {
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($album, 'album'); /// Gestion des slugs
            // Gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'multimedia');
                if ($album->getMedia())
                    $this->gestionMedia->removeUpload($album->getMedia(), 'multimedia');

                $album->setMedia($media);
            }

            $albumRepository->save($album, true);

            $this->gestionCache->cacheAlbum('fr', true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("L'album '{$album->getTitre()}' a été modifié avec succès:");

            return $this->redirectToRoute('app_backend_album_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_album/edit.html.twig', [
            'album' => $album,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_album_delete', methods: ['POST'])]
    public function delete(Request $request, Album $album, AlbumRepository $albumRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$album->getId(), $request->request->get('_token'))) {
            if ($album->getPageIndex()){
                $this->flasher
                    ->create('sweetalert')
                    ->icon('error')
                    ->addError("Veuillez supprimer la correspondante anglaise de cet album avant de pourvoir le supprimer");
                // Redirection a modifier
                return $this->redirectToRoute("app_backend_album_index",[], Response::HTTP_SEE_OTHER);
            }

            if ($album->getPhotos()){
                $this->flasher
                    ->create('sweetalert')
                    ->addError("Veuillez supprimer les photos de cet album avant de pouvoir le supprimer");

                return $this->redirectToRoute("app_backend_photo_index",['slug_album' => $album->getSlug()],Response::HTTP_SEE_OTHER);
            }
            $albumRepository->remove($album, true);

            if ($album->getMedia())
                $this->gestionMedia->removeUpload($album->getMedia(), 'multimedia');

            $this->gestionCache->cacheAlbum('fr', true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("L'album '{$album->getTitre()}' a été modifié avec succès!");
        }

        return $this->redirectToRoute('app_backend_album_index', [], Response::HTTP_SEE_OTHER);
    }
}
