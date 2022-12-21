<?php

namespace App\Controller\Backend;

use App\Entity\EnActualite;
use App\Form\EnActualiteType;
use App\Repository\EnActualiteRepository;
use App\Repository\FrActualiteRepository;
use App\Repository\TraductionRepository;
use App\Services\GestionCache;
use App\Services\GestionMedia;
use App\Services\Utility;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/en/actualite')]
class BackendEnActualiteController extends AbstractController
{
    const TRADUCTION_ENTITY = 'actualite';
    public function __construct(
        private GestionMedia $gestionMedia, private GestionCache $gestionCache, private Utility $utility,
        private FrActualiteRepository $frActualiteRepository, private Flasher $flasher,
        private TraductionRepository $traductionRepository
    )
    {
    }

    #[Route('/', name: 'app_backend_en_actualite_index', methods: ['GET'])]
    public function index(EnActualiteRepository $enActualiteRepository): Response
    {
        return $this->render('backend_en_actualite/index.html.twig', [
            'en_actualites' => $enActualiteRepository->findBy([],['id'=>"DESC"]),
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/new', name: 'app_backend_en_actualite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EnActualiteRepository $enActualiteRepository): Response
    {
        $enActualite = new EnActualite();
        $form = $this->createForm(EnActualiteType::class, $enActualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verification de l'existence de la version francaise
            $fractualite = $this->frActualiteRepository->findOneBy(['id' => $request->request->get('_traduction')]);
            if (!$fractualite){
                $this->flasher
                    ->create('sweetalert')
                    ->icon('error')
                    ->addError("Warning! please associate the french version.");

                return $this->redirectToRoute('app_backend_en_actualite_index',[], Response::HTTP_SEE_OTHER);
            }

            $this->utility->slug($enActualite, 'enActualite', true); // Gestion des slugs
            // Gestions des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'actualite');
                $enActualite->setMedia($media);
            }

            $enActualiteRepository->save($enActualite, true);

            $this->utility->traductionSave($fractualite, $enActualite, self::TRADUCTION_ENTITY);

            return $this->redirectToRoute('app_backend_en_actualite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_actualite/new.html.twig', [
            'en_actualite' => $enActualite,
            'form' => $form,
            'fr_actualites' => $this->frActualiteRepository->findListInactif(),
            'traduction' => null
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_actualite_show', methods: ['GET'])]
    public function show(EnActualite $enActualite): Response
    {
        return $this->render('backend_en_actualite/show.html.twig', [
            'en_actualite' => $enActualite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_en_actualite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EnActualite $enActualite, EnActualiteRepository $enActualiteRepository): Response
    {
        $form = $this->createForm(EnActualiteType::class, $enActualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($enActualite, 'enActualite', true); // Gestion des slugs
            // Gestion des medias
            $mediaFile = $form->get('media')->getData();
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'actualite');
                if ($enActualite->getMedia())
                    $this->gestionMedia->removeUpload($enActualite->getMedia(), 'actualite');

                $enActualite->setMedia($media);
            }

            $enActualiteRepository->save($enActualite, true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("Item '{$enActualite->getTitre()}' has been successfully updated:");

            return $this->redirectToRoute('app_backend_en_actualite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_actualite/edit.html.twig', [
            'en_actualite' => $enActualite,
            'form' => $form,
            'fr_actualites' => $this->frActualiteRepository->findAll(),
            'traduction' => $this->utility->traductionSelect($enActualite->getPageIndex(), self::TRADUCTION_ENTITY)
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_actualite_delete', methods: ['POST'])]
    public function delete(Request $request, EnActualite $enActualite, EnActualiteRepository $enActualiteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enActualite->getId(), $request->request->get('_token'))) {
            $enActualiteRepository->remove($enActualite, true);
        }

        return $this->redirectToRoute('app_backend_en_actualite_index', [], Response::HTTP_SEE_OTHER);
    }
}
