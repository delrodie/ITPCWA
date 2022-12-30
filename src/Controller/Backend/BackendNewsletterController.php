<?php

namespace App\Controller\Backend;

use App\Repository\NewsletterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/newsletter')]
class BackendNewsletterController extends AbstractController
{
    public function __construct(
        private NewsletterRepository $newsletterRepository
    )
    {
    }

    #[Route('/', name: 'app_backend_newsletter')]
    public function index(): Response
    {
        return $this->render('backend/newsletter_compte.html.twig',[
            'newsletters' => $this->newsletterRepository->findBy([],['id'=>"DESC"])
        ]);
    }
}
