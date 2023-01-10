<?php

namespace App\Controller\Backend;

use App\Entity\MaintenanceExcept;
use App\Form\MaintenanceExceptType;
use App\Repository\MaintenanceExceptRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/except')]
class BackendMaintenanceExceptController extends AbstractController
{
    #[Route('/', name: 'app_backend_maintenance_except_index', methods: ['GET', 'POST'])]
    public function index(Request $request, MaintenanceExceptRepository $maintenanceExceptRepository): Response
    {
        $maintenanceExcept = new MaintenanceExcept();
        $form = $this->createForm(MaintenanceExceptType::class, $maintenanceExcept);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $maintenanceExceptRepository->save($maintenanceExcept, true);

            return $this->redirectToRoute('app_backend_maintenance_except_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_maintenance_except/index.html.twig', [
            'maintenances' => $maintenanceExceptRepository->findAll(),
            'maintenance_except' => $maintenanceExcept,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_backend_maintenance_except_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MaintenanceExceptRepository $maintenanceExceptRepository): Response
    {
        $maintenanceExcept = new MaintenanceExcept();
        $form = $this->createForm(MaintenanceExceptType::class, $maintenanceExcept);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $maintenanceExceptRepository->save($maintenanceExcept, true);

            return $this->redirectToRoute('app_backend_maintenance_except_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_maintenance_except/new.html.twig', [
            'maintenance_except' => $maintenanceExcept,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_maintenance_except_show', methods: ['GET'])]
    public function show(MaintenanceExcept $maintenanceExcept): Response
    {
        return $this->render('backend_maintenance_except/show.html.twig', [
            'maintenance_except' => $maintenanceExcept,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_maintenance_except_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MaintenanceExcept $maintenanceExcept, MaintenanceExceptRepository $maintenanceExceptRepository): Response
    {
        $form = $this->createForm(MaintenanceExceptType::class, $maintenanceExcept);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $maintenanceExceptRepository->save($maintenanceExcept, true);

            return $this->redirectToRoute('app_backend_maintenance_except_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_maintenance_except/edit.html.twig', [
            'maintenance_except' => $maintenanceExcept,
            'form' => $form,
            'maintenances' => $maintenanceExceptRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_backend_maintenance_except_delete', methods: ['POST'])]
    public function delete(Request $request, MaintenanceExcept $maintenanceExcept, MaintenanceExceptRepository $maintenanceExceptRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$maintenanceExcept->getId(), $request->request->get('_token'))) {
            $maintenanceExceptRepository->remove($maintenanceExcept, true);
        }

        return $this->redirectToRoute('app_backend_maintenance_except_index', [], Response::HTTP_SEE_OTHER);
    }
}
