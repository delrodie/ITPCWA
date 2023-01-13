<?php

namespace App\Controller\Frontend;

use App\Entity\Candidat;
use App\Form\PostulerType;
use App\Services\GestionCache;
use App\Services\GestionCandidature;
use App\Services\Utility;
use Flasher\Prime\Flasher;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/recruitment')]
class FrontendRecruitmentController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache, private Utility $utility, private Flasher $flasher,
        private TranslatorInterface $translator, private GestionCandidature $candidature,
        private PaginatorInterface $paginator
    )
    {
    }

    #[Route('/{_locale}', name: 'app_frontend_recruitment')]
    public function index(Request $request, $_locale): Response
    {
        $datas = $this->gestionCache->cacheJob($_locale, true);
        $jobs = $this->paginator->paginate(
            $datas,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('frontend/recruitments.html.twig',[
            'locale' => $_locale,
            'recruitments' => $jobs,
            'pagination' => false,
            'active' => 'recruitement'
        ]);
    }

    #[Route('/{_locale}/{slug}', name: 'app_frontend_recruitment_show', methods: ['GET', 'POST'])]
    public function show(Request $request, $_locale, $slug)
    {
        $candidat = new Candidat();
        $form = $this->createForm(PostulerType::class, $candidat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            return $this->candidature->postulerRequest($form, $request, $candidat);

        }
        return $this->renderForm('frontend/recruitment_show.html.twig',[
            'locale' => $_locale,
            'recruitment' => $this->gestionCache->cacheJobItem($_locale, $slug),
            'form' => $form,
            'active' => 'recruitement'
        ]);
    }
}
