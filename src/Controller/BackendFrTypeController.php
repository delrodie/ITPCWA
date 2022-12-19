<?php

namespace App\Controller;

use App\Entity\FrType;
use App\Form\FrTypeType;
use App\Repository\FrTypeRepository;
use App\Services\Utility;
use Faker\Factory;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/fr/type')]
class BackendFrTypeController extends AbstractController
{
    public function __construct(
        private Flasher $flasher, private Utility $utility
    )
    {
    }

    #[Route('/', name: 'app_backend_fr_type_index', methods: ['GET','POST'])]
    public function index(Request $request, FrTypeRepository $frTypeRepository): Response
    {
        $frType = new FrType();
        $form = $this->createForm(FrTypeType::class, $frType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($frType, 'frType');
            $frTypeRepository->save($frType, true);

            $this->flasher
                ->create('sweetalert')
                ->icon('warning')
                ->addSuccess('Pour que cet enregistrement soit actif il faudrait enregistrer la version anglaise')
                ;

            $this->flasher
                ->create('notyf')
                ->addSuccess('Enregistrement effectué avec succès!');

            return $this->redirectToRoute('app_backend_fr_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_type/index.html.twig', [
            'fr_types' => $frTypeRepository->findAll(),
            'fr_type' => $frType,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_backend_fr_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FrTypeRepository $frTypeRepository): Response
    {
        $frType = new FrType();
        $form = $this->createForm(FrTypeType::class, $frType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $frTypeRepository->save($frType, true);

            return $this->redirectToRoute('app_backend_fr_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_type/new.html.twig', [
            'fr_type' => $frType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_type_show', methods: ['GET'])]
    public function show(FrType $frType): Response
    {
        return $this->render('backend_fr_type/show.html.twig', [
            'fr_type' => $frType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_fr_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FrType $frType, FrTypeRepository $frTypeRepository): Response
    {
        $form = $this->createForm(FrTypeType::class, $frType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($frType, 'frType');
            $frTypeRepository->save($frType, true);

            $this->flasher
                ->create('sweetalert')
                ->icon('warning')
                ->addSuccess('Merci de modifier la version anglaise');

            $this->flasher
                ->create('notyf')
                ->addSuccess('Modification effectuée avec succès!');

            return $this->redirectToRoute('app_backend_fr_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_fr_type/edit.html.twig', [
            'fr_type' => $frType,
            'form' => $form,
            'fr_types' => $frTypeRepository->findAll()
        ]);
    }

    #[Route('/{id}', name: 'app_backend_fr_type_delete', methods: ['POST'])]
    public function delete(Request $request, FrType $frType, FrTypeRepository $frTypeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$frType->getId(), $request->request->get('_token'))) {
            $frTypeRepository->remove($frType, true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("Le type de rubrique '{$frType->getTitre()}' a été supprimé avec succès!");
        }

        return $this->redirectToRoute('app_backend_fr_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
