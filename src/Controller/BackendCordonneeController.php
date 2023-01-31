<?php

namespace App\Controller;

use App\Entity\Cordonnee;
use App\Form\CordonneeType;
use App\Repository\CordonneeRepository;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/cordonnee')]
class BackendCordonneeController extends AbstractController
{
    public function __construct(private Utility $utility, private Flasher $flasher)
    {
    }

    #[Route('/', name: 'app_backend_cordonnee_index', methods: ['GET','POST'])]
    public function index(CordonneeRepository $cordonneeRepository): Response
    {
        $cordonnee = $cordonneeRepository->findOneBy([],['id'=>"DESC"]);
        if (!$cordonnee)
            return $this->redirectToRoute("app_backend_cordonnee_new",[], Response::HTTP_SEE_OTHER);
        else
            return $this->redirectToRoute("app_backend_cordonnee_show",['id'=>$cordonnee->getId()], Response::HTTP_SEE_OTHER);

        //return $this->render('backend_cordonnee/index.html.twig', [
        //    'cordonnees' => $cordonneeRepository->findAll(),
        //ss]);
    }

    #[Route('/new', name: 'app_backend_cordonnee_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CordonneeRepository $cordonneeRepository): Response
    {
        $cordonnee = new Cordonnee();
        $form = $this->createForm(CordonneeType::class, $cordonnee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cordonneeRepository->save($cordonnee, true);

            $this->flasher->create('notyf')->addSuccess("Les coordonnées ont été ajoutées avec succès!");

            return $this->redirectToRoute('app_backend_cordonnee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_cordonnee/new.html.twig', [
            'cordonnee' => $cordonnee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_cordonnee_show', methods: ['GET'])]
    public function show(Cordonnee $cordonnee): Response
    {
        return $this->render('backend_cordonnee/show.html.twig', [
            'cordonnee' => $cordonnee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_cordonnee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cordonnee $cordonnee, CordonneeRepository $cordonneeRepository): Response
    {
        $form = $this->createForm(CordonneeType::class, $cordonnee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cordonneeRepository->save($cordonnee, true);

            return $this->redirectToRoute('app_backend_cordonnee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_cordonnee/edit.html.twig', [
            'cordonnee' => $cordonnee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_cordonnee_delete', methods: ['POST'])]
    public function delete(Request $request, Cordonnee $cordonnee, CordonneeRepository $cordonneeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cordonnee->getId(), $request->request->get('_token'))) {
            $cordonneeRepository->remove($cordonnee, true);
        }

        return $this->redirectToRoute('app_backend_cordonnee_index', [], Response::HTTP_SEE_OTHER);
    }
}
