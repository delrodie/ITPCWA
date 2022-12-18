<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    #[Route('/{_locale}/sitemap', name: 'app_sitemap')]
    public function index($_locale): Response
    {
        return $this->render('frontend/sitemap.html.twig',[
            'locale' => $_locale
        ]);
    }
}
