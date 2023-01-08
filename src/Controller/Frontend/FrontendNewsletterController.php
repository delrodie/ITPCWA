<?php

namespace App\Controller\Frontend;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\NewsletterRepository;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/newsletter')]
class FrontendNewsletterController extends AbstractController
{
    #[Route('/{_locale}', name: 'app_frontend_newsletter', methods: ['GET','POST'])]
    public function index(
        Request $request, NewsletterRepository $newsletterRepository, $_locale, Flasher $flasher,
        TranslatorInterface $translator
    ): Response
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request); //dump($request->headers->get('referer'));

        if ($form->isSubmitted() && $form->isValid()){ //dd();
            $link = $request->headers->get('referer');

            // Verification de l'unicité de l'adresse email
            $verif = $newsletterRepository->findOneBy(['email' => $newsletter->getEmail()]);
            if ($verif){
                $flasher->create('sweetalert')
                    ->icon('error')
                    ->addError($translator->trans("You've already subscribed to newsletters"));

                return $this->redirect($link);
            }

            $newsletter->setStatut(true);
            $newsletterRepository->save($newsletter, true);

            $flasher->create('sweetalert')
                ->icon("success")
                ->addSuccess($translator->trans("Your email has been registered successfully!"));

            return $this->redirect($link);
        }
        return $this->renderForm('frontend/newsletter.html.twig',[
            'locale' => $_locale,
            'form' => $form,
            'active' => "newsletter"
        ]);
    }
}
