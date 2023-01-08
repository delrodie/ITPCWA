<?php

namespace App\Controller\Backend;

use App\Entity\FrInfo;
use App\Form\FrInfoType;
use App\Repository\FrInfoRepository;
use App\Services\GestionCache;
use Flasher\Prime\FlasherInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/fr/info')]
class BackendFrInfoController extends AbstractController
{
    public function __construct(
        private FlasherInterface $flasher, private GestionCache $gestionCache
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/', name: 'app_backend_fr_info_index', methods: ['GET','POST'])]
    public function index(Request $request, FrInfoRepository $frInfoRepository): Response
    {
        $frInfo = new FrInfo();
        $form = $this->createForm(FrInfoType::class, $frInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($frInfo);
            $frInfoRepository->save($frInfo, true);

            // Suppression du cache des messages flash
            $this->gestionCache->cacheMessages('fr', true);

            $this->flasher
                ->create('sweetalert')
                ->icon('success')
                //->toast(true, 'top-end', true)
                ->addSuccess("Nous vous invitons à enregisrer la version anglaise!")
                ;

            $this->flasher
                ->create('notyf')
                ->addSuccess("La version française de l'information a été enregistrée avec succès!");

            return $this->redirectToRoute('app_backend_fr_info_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_info/index.html.twig', [
            'fr_infos' => $frInfoRepository->findBy([],['fin'=>"DESC"]),
            'fr_info' => $frInfo,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_backend_fr_info_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FrInfoRepository $frInfoRepository): Response
    {
        $frInfo = new FrInfo();
        $form = $this->createForm(FrInfoType::class, $frInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $frInfoRepository->save($frInfo, true);

            return $this->redirectToRoute('app_backend_fr_info_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_info/new.html.twig', [
            'fr_info' => $frInfo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_info_show', methods: ['GET'])]
    public function show(FrInfo $frInfo): Response
    {
        return $this->render('backend_fr_info/show.html.twig', [
            'fr_info' => $frInfo,
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}/edit', name: 'app_backend_fr_info_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FrInfo $frInfo, FrInfoRepository $frInfoRepository): Response
    {
        $form = $this->createForm(FrInfoType::class, $frInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $frInfoRepository->save($frInfo, true);

            // Suppression du cache des messages en francais
            $this->gestionCache->cacheMessages('fr',true);

            $this->flasher
                ->create('sweetalert')
                ->icon('success')
                ->addSuccess('Nous vous invitons à modifier la version anglaise')
                ;

            $this->flasher
                ->create('notyf')
                ->addSuccess("Modification effectuée avec succès!");

            return $this->redirectToRoute('app_backend_fr_info_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_info/edit.html.twig', [
            'fr_info' => $frInfo,
            'form' => $form,
            'fr_infos' => $frInfoRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_info_delete', methods: ['POST'])]
    public function delete(Request $request, FrInfo $frInfo, FrInfoRepository $frInfoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$frInfo->getId(), $request->request->get('_token'))) {
            $frInfoRepository->remove($frInfo, true);

            $this->gestionCache->cacheMessages('fr', true);

            $this->flasher
                ->create('sweetalert')
                ->toast(true, 'top-end')
                ->addSuccess('Message supprimer avec succès!');
        }

        return $this->redirectToRoute('app_backend_fr_info_index', [], Response::HTTP_SEE_OTHER);
    }
}
