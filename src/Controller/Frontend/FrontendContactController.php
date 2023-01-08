<?php

namespace App\Controller\Frontend;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/contact')]
class FrontendContactController extends AbstractController
{
    #[Route('/{_locale}', name: 'app_frontend_contact', methods: ['GET','POST'])]
    public function index(
        Request $request, $_locale, ContactRepository $contactRepository, Flasher $flasher,
        TranslatorInterface $translator
    ): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $contactRepository->save($contact, true);

            $flasher->create('sweetalert')
                ->icon('success')
                ->addSuccess($translator->trans("Your message has been sent successfully!"));

            return $this->redirectToRoute('app_frontend_contact',['_locale'=>$_locale], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('frontend/contact.html.twig',[
            'locale' => $_locale,
            'form' => $form,
            'active' => "contact"
        ]);
    }
}
