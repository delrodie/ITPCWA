<?php

namespace App\Controller\Backend;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/contact')]
class BackendContactController extends AbstractController
{
    #[Route('/', name: 'app_backend_contact_index')]
    public function index(ContactRepository $contactRepository): Response
    {
        return $this->render('backend/contact_messages.html.twig',[
            'contacts' => $contactRepository->findBy([],['id'=>"DESC"])
        ]);
    }

    #[Route('/{id}', name: "app_backend_contact_details")]
    public function details(Contact $contact)
    {
        return $this->render('backend/contact_details.html.twig', compact('contact'));
    }
}
