<?php

namespace App\Controller\Backend;

use App\Entity\Photo;
use App\Form\PhotoType;
use App\Repository\AlbumRepository;
use App\Repository\PhotoRepository;
use App\Services\GestionCache;
use App\Services\GestionMedia;
use App\Services\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/photo')]
class BackendPhotoController extends AbstractController
{
    public function __construct(
        private GestionMedia $gestionMedia, private Flasher $flasher, private GestionCache $gestionCache,
        private AlbumRepository $albumRepository, private EntityManagerInterface $entityManager,
        private Utility $utility
    )
    {
    }

    #[Route('/{slug_album}', name: 'app_backend_photo_index', methods: ['GET'])]
    public function index(PhotoRepository $photoRepository, $slug_album): Response
    {
        $album = $this->getAlbum($slug_album);

        return $this->render('backend_photo/index.html.twig', [
            'photos' => $photoRepository->findByAlbumSlug($album->getSlug()),
            'album' => $album
        ]);
    }

    #[Route('/new/{slug_album}', name: 'app_backend_photo_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PhotoRepository $photoRepository, $slug_album): Response
    {
        $album = $this->getAlbum($slug_album);

        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $files = $form->get('media')->getData();
            if (!$files){
                $this->flasher
                    ->create('sweetalert')
                    ->icon('error')
                    ->addError("Attention, aucune image n'a été téléchargée!");

                return $this->redirectToRoute('app_backend_photo_new',['slug_album'=>$slug_album], Response::HTTP_SEE_OTHER);
            }

            $i=0;
            foreach ($files as $file){
                $photo = new Photo();
                $media = $this->gestionMedia->upload($file, 'multimedia');
                $photo->setImage($media);
                $photo->setAlbum($album);

                $this->entityManager->persist($photo);
            }

            $this->entityManager->flush();

            $this->flasher
                ->create('notyf')
                ->addSuccess("Les photos ont été ajoutée avec succès à l'album '{$album->getTitre()}'");

            return $this->redirectToRoute('app_backend_album_show', ['id'=>$album->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_photo/new.html.twig', [
            'photo' => $photo,
            'form' => $form,
            'album' => $album
        ]);
    }

    #[Route('/{id}', name: 'app_backend_photo_show', methods: ['GET'])]
    public function show(Photo $photo): Response
    {
        return $this->render('backend_photo/show.html.twig', [
            'photo' => $photo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_photo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Photo $photo, PhotoRepository $photoRepository): Response
    {
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoRepository->save($photo, true);

            return $this->redirectToRoute('app_backend_photo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_photo/edit.html.twig', [
            'photo' => $photo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_photo_delete', methods: ['POST'])]
    public function delete(Request $request, Photo $photo, PhotoRepository $photoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$photo->getId(), $request->request->get('_token'))) {
            $photoRepository->remove($photo, true);
            if ($photo->getImage())
                $this->gestionMedia->removeUpload($photo->getImage(), 'multimedia');

            $this->flasher
                ->create('notyf')
                ->addSuccess("Image supprimée avec succès!");

            return $this->redirectToRoute('app_backend_album_show',['id' => $photo->getAlbum()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_backend_photo_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param $string
     * @return \App\Entity\Album|\App\Entity\FrActualite|\App\Entity\FrJob|\App\Entity\FrProjet|\App\Entity\FrType|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function getAlbum($string)
    {
        $fr = $this->albumRepository->findOneBy(['slug' => (string) $string]);
        $en = $this->utility->traductionRoute('album', (int) $string);
        if ($fr) $album = $fr;
        elseif ($en) $album = $en;
        else{
            $this->flasher
                ->create('sweetalert')
                ->addError("Aucune photo trouvée!");

            return $this->redirectToRoute('app_backend_album_index',[],Response::HTTP_SEE_OTHER);
        }

        return $album;
    }
}
