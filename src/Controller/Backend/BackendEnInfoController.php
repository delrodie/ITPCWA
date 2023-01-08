<?php

namespace App\Controller\Backend;

use App\Entity\EnInfo;
use App\Form\EnInfoType;
use App\Repository\EnInfoRepository;
use App\Services\GestionCache;
use Flasher\Prime\FlasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/en/info')]
class BackendEnInfoController extends AbstractController
{
    public function __construct(
        private FlasherInterface $flasher, private GestionCache $gestionCache
    )
    {
    }

    #[Route('/', name: 'app_backend_en_info_index', methods: ['GET','POST'])]
    public function index(Request $request, EnInfoRepository $enInfoRepository): Response
    {
        $enInfo = new EnInfo();
        $form = $this->createForm(EnInfoType::class, $enInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $enInfoRepository->save($enInfo, true);

            $this->gestionCache->cacheMessages('en',true);

            $this->flasher
                ->create('sweetalert')
                ->icon('success')
                ->addSuccess('We invite you to register the french version if it is not already done!');

            $this->flasher
                ->create('notyf')
                ->addSuccess('The french version of this information has been saved successfully!');

            return $this->redirectToRoute('app_backend_en_info_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_info/index.html.twig', [
            'en_infos' => $enInfoRepository->findAll(),
            'en_info' => $enInfo,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_backend_en_info_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EnInfoRepository $enInfoRepository): Response
    {
        $enInfo = new EnInfo();
        $form = $this->createForm(EnInfoType::class, $enInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $enInfoRepository->save($enInfo, true);

            return $this->redirectToRoute('app_backend_en_info_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_info/new.html.twig', [
            'en_info' => $enInfo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_info_show', methods: ['GET'])]
    public function show(EnInfo $enInfo): Response
    {
        return $this->render('backend_en_info/show.html.twig', [
            'en_info' => $enInfo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_en_info_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EnInfo $enInfo, EnInfoRepository $enInfoRepository): Response
    {
        $form = $this->createForm(EnInfoType::class, $enInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $enInfoRepository->save($enInfo, true);

            $this->gestionCache->cacheMessages('en',true);

            $this->flasher
                ->create('sweetalert')
                ->icon('success')
                ->addSuccess('We invite you to update the french version if it is not already done!');

            $this->flasher
                ->create('notyf')
                ->addSuccess('The french version of this information has been updated successfully!');

            return $this->redirectToRoute('app_backend_en_info_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_info/edit.html.twig', [
            'en_info' => $enInfo,
            'form' => $form,
            'en_infos' => $enInfoRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_info_delete', methods: ['POST'])]
    public function delete(Request $request, EnInfo $enInfo, EnInfoRepository $enInfoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enInfo->getId(), $request->request->get('_token'))) {
            $enInfoRepository->remove($enInfo, true);

            $this->gestionCache->cacheMessages('en', true);

            $this->flasher
                ->create('sweetalert')
                ->toast(true)
                ->addSuccess("Information delete successfully!");
        }

        return $this->redirectToRoute('app_backend_en_info_index', [], Response::HTTP_SEE_OTHER);
    }
}
