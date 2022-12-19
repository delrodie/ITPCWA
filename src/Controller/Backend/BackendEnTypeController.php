<?php

namespace App\Controller\Backend;

use App\Entity\EnType;
use App\Form\EnTypeType;
use App\Repository\EnTypeRepository;
use App\Repository\FrTypeRepository;
use App\Repository\TraductionRepository;
use App\Services\GestionCache;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/en/type')]
class BackendEnTypeController extends AbstractController
{
    const TRADUCTION_ENTITY = 'type';

    public function __construct(
        private Utility $utility, private GestionCache $gestionCache, private Flasher $flasher,
        private FrTypeRepository $frTypeRepository, private TraductionRepository $traductionRepository
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/', name: 'app_backend_en_type_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EnTypeRepository $enTypeRepository): Response
    {
        $enType = new EnType();
        $form = $this->createForm(EnTypeType::class, $enType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $frtype = $this->frTypeRepository->findOneBy(['id' => $request->get('_frtype')]) ;
            if (!$frtype){
                $this->flasher
                    ->create('sweetalert')
                    ->icon('warning')
                    ->addSuccess("Attention, veuillez choisir la correspondance française!");
                    ;
            }
            $this->utility->slug($enType, 'enType');
            $enTypeRepository->save($enType, true);

            $this->gestionCache->cacheEnType(true);

            $this->utility->traductionSave($frtype, $enType, self::TRADUCTION_ENTITY);

            $this->flasher
                ->create('notyf')
                ->addSuccess("The type '{$enType->getTitre()}' was successfully added!");
            ;

            return $this->redirectToRoute('app_backend_en_type_index', [], Response::HTTP_SEE_OTHER);
        }
        //dd($this->frTypeRepository->findListInactif());
        return $this->renderForm('backend_en_type/index.html.twig', [
            'en_types' => $enTypeRepository->findAll(),
            'en_type' => $enType,
            'form' => $form,
            'frTypes' => $this->frTypeRepository->findListInactif(),
            'traduction' => null
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/new', name: 'app_backend_en_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EnTypeRepository $enTypeRepository): Response
    {
        $enType = new EnType();
        $form = $this->createForm(EnTypeType::class, $enType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->slug($enType, 'enType');
            $enTypeRepository->save($enType, true);
            $this->gestionCache->cacheEnType(true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("Le type '{$enType->getTitre()}' a été ajouté avec succès!");
            ;

            return $this->redirectToRoute('app_backend_en_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_en_type/new.html.twig', [
            'en_type' => $enType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_en_type_show', methods: ['GET'])]
    public function show(EnType $enType): Response
    {
        return $this->render('backend_en_type/show.html.twig', [
            'en_type' => $enType,
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}/edit', name: 'app_backend_en_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EnType $enType, EnTypeRepository $enTypeRepository): Response
    {
        $form = $this->createForm(EnTypeType::class, $enType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $frtype = $this->frTypeRepository->findOneBy(['id' => $request->get('_frtype')]) ;
            if (!$frtype){
                $this->flasher
                    ->create('sweetalert')
                    ->icon('warning')
                    ->addSuccess("Attention, veuillez choisir la correspondance française!");
                ;
            }
            $this->utility->slug($enType, 'enType');
            $enTypeRepository->save($enType, true);

            $this->gestionCache->cacheEnType(true);

            //$this->utility->traductionSave($frtype, $enType, self::TRADUCTION_ENTITY);

            $this->flasher
                ->create('notyf')
                ->addSuccess("The type '{$enType->getTitre()}' was successfully updated!");
            ;

            return $this->redirectToRoute('app_backend_en_type_index', [], Response::HTTP_SEE_OTHER);
        }

        //dd($this->traductionRepository->findOneBy(['page' => $enType->getPageIndex(), 'route' => self::TRADUCTION_ENTITY]));
        //dd($enType);
        return $this->renderForm('backend_en_type/edit.html.twig', [
            'en_type' => $enType,
            'form' => $form,
            'en_types' => $enTypeRepository->findAll(),
            'frTypes' => $this->frTypeRepository->findAll(),
            'traduction' => $this->traductionRepository->findOneBy(['page' => $enType->getPageIndex(), 'route' => self::TRADUCTION_ENTITY])
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/{id}', name: 'app_backend_en_type_delete', methods: ['POST'])]
    public function delete(Request $request, EnType $enType, EnTypeRepository $enTypeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enType->getId(), $request->request->get('_token'))) {
            $enTypeRepository->remove($enType, true);
            $this->utility->traductionDelete($enType, self::TRADUCTION_ENTITY);

            $this->gestionCache->cacheEnType(true);

            $this->flasher
                ->create('notyf')
                ->addSuccess("Type '{$enType->getTitre()}' was deleted successfully!");
        }

        return $this->redirectToRoute('app_backend_en_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
