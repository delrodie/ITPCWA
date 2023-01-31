<?php

namespace App\Controller\Frontend;

use App\Repository\AlbumRepository;
use App\Repository\EnActualiteRepository;
use App\Repository\EnAlbumRepository;
use App\Repository\EnEquipeRepository;
use App\Repository\EnJobRepository;
use App\Repository\EnPresentationRepository;
use App\Repository\EnProjetRepository;
use App\Repository\EnRessourceRepository;
use App\Repository\FrActualiteRepository;
use App\Repository\FrEquipeRepository;
use App\Repository\FrJobRepository;
use App\Repository\FrPresentationRepository;
use App\Repository\FrProjetRepository;
use App\Repository\FrRessourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'app_sitemap_xml', defaults: ["_format"=>"xml"])]
    public function index(
        Request $request,
        FrPresentationRepository $frPresentationRepository,
        EnPresentationRepository $enPresentationRepository,
        EnActualiteRepository $enActualiteRepository,
        FrActualiteRepository $frActualiteRepository,
        EnProjetRepository $enProjetRepository,
        FrProjetRepository $frProjetRepository,
        FrRessourceRepository $frRessourceRepository,
        EnRessourceRepository $enRessourceRepository,
        FrJobRepository $frJobRepository,
        EnJobRepository $enJobRepository,
        AlbumRepository $albumRepository,
        EnAlbumRepository $enAlbumRepository,
        FrEquipeRepository $frEquipeRepository,
        EnEquipeRepository $enEquipeRepository
    ): Response
    {
        $hostname = $request->getSchemeAndHttpHost(); // recuperation du nom de l'hôte depuis l'url
        $urls = []; // Initialisation du tableau pour lister les urls

        // Ajout des urls statiques
        $urls[] = ['loc' => $this->generateUrl('app_home')];

        //$urls[] = ['loc']

        $locale = [
            0 => 'fr',
            1 => 'en'
        ];

        foreach ($locale as $loc ){ //dd($loc);
            if ($loc === 'fr'){
                $presentations = $frPresentationRepository->findAll();
                $actualites = $frActualiteRepository->findAll();
                $projets = $frProjetRepository->findListActif();
                $ressources = $frRessourceRepository->findAll();
                $jobs = $frJobRepository->findListActif();
                $rubrique = 'actualites';
                $albums = $albumRepository->findListActif();
                $equipes = $frEquipeRepository->findListActif();
            }else{
                $presentations = $enPresentationRepository->findAll();
                $actualites = $enActualiteRepository->findAll();
                $projets = $enProjetRepository->findListActif();
                $ressources = $enRessourceRepository->findAll();
                $jobs = $enJobRepository->findListActif();
                $rubrique = 'news';
                $albums = $enAlbumRepository->findListActif();
                $equipes = $enEquipeRepository->findListActif();
            }

            $urls[] = ['loc' => $this->generateUrl('app_frontend_index',['_locale' => $loc])];
            $urls[] = ['loc' => $this->generateUrl('app_sitemap',['_locale' => $loc])];
            $urls[] = ['loc' => $this->generateUrl('app_frontend_actualite_index',['_locale'=> $loc, 'rubrique' => $rubrique])];
            $urls[] = ['loc' => $this->generateUrl('app_frontend_ressource',['_locale' => $loc])];
            $urls[] = ['loc' => $this->generateUrl('app_frontend_recruitment',['_locale'=>$loc])];
            $urls[] = ['loc' => $this->generateUrl('app_frontend_bienvenue',['_locale'=>$loc])];
            $urls[] = ['loc' => $this->generateUrl('app_frontend_contact',['_locale'=>$loc])];
            $urls[] = ['loc' => $this->generateUrl('app_frontend_multimedia',['_locale'=>$loc, 'rubrique' => 'picture'])];
            $urls[] = ['loc' => $this->generateUrl('app_frontend_equipe_index', ['_locale' => $loc])];
            foreach ($presentations as $presentation){
                $images = [
                    'loc' => '/uploads/presentation/'.$presentation->getMedia(),
                    'title' => $presentation->getTitre()
                ];
                $urls[] = [
                    'loc' => $this->generateUrl('app_frontend_presentation',['_locale'=>$loc, 'slug'=>$presentation->getSlug()]),
                    'lastmod' => $presentation->getCreatedAt()->format('Y-m-d'),
                    'image' => $images
                ];
            }

            foreach ($actualites as $actualite){
                $images = [
                    'loc' => '/uploads/actualite/'.$actualite->getMedia(),
                    'title' => $actualite->getTitre()
                ];
                $urls[] = [
                    'loc' => $this->generateUrl('app_frontend_actualite_show',[
                        '_locale' => $loc,
                        'rubrique' => $rubrique,
                        'slug' => $actualite->getSlug()
                    ]),
                    'lastmod' => $actualite->getCreatedAt()->format('Y-m-d'),
                    'image' => $images
                ];
            }

            foreach ($projets as $projet){
                if ($projet->getUpdatedAt()) $date = $projet->getUpdatedAt();
                else $date = $projet->getCreatedAt();

                $images = [
                    'loc' => '/uploads/projet/'.$projet->getMedia(),
                    'title' => $projet->getTitre()
                ];
                $urls[] = [
                    'loc' => $this->generateUrl('app_frontend_projet_show',[
                        '_locale' => $loc,
                        'slug' => $projet->getSlug()
                    ]),
                    'lastmod' => $date->format('Y-m-d'),
                    'image' => $images
                ];
            }

            foreach ($jobs as $job){
                if ($job->getUpdatedAt()) $date = $job->getUpdatedAt();
                else $date = $job->getCreatedAt();
                $urls[]=[
                    'loc' => $this->generateUrl('app_frontend_recruitment_show',[
                        '_locale' => $loc,
                        'slug' => $job->getSlug()
                    ]),
                    'lastmod' => $date->format('Y-m-d')
                ];
            }

            foreach ($albums as $album){
                if ($album->getUpdatedAt()) $date = $album->getUpdatedAt();
                else $date = $album->getCreatedAt();

                $images = [
                    'loc' => '/uploads/multimedia/'.$album->getMedia(),
                    'title' => $album->getTitre()
                ];
                $urls[] = [
                    'loc' => $this->generateUrl('app_frontend_multimedia_show',[
                        '_locale' => $loc,
                        'slug' => $album->getSlug(),
                        'rubrique' => "picture"
                    ]),
                    'lastmod' => $date->format('Y-m-d'),
                    'image' => $images
                ];
            }

            foreach ($equipes as $equipe){
                $date = $equipe->getCreatedAt()->format('Y-m-d');

                $images = [
                    'loc' => '/uploads/equipe/'.$equipe->getMedia(),
                    'title' => $equipe->getTitre()
                ];
                $urls[] = [
                    'loc' => $this->generateUrl('app_frontend_equipe_show',[
                        '_locale' => $loc,
                        'slug' => $equipe->getSlug()
                    ]),
                    'lastmod' => $date,
                    'image' => $images
                ];
            }
        }

        $response = new Response(
            $this->renderView('frontend/sitemap_xml.html.twig',[
                'urls' => $urls,
                'hostname' => $hostname,
            ]), 200
        ); //dd($response);

        //$response->headers->set('Content-Type', 'text/xml');
        $response->headers->add(['Content-Type' => 'text/xml']);

        //dd($response);
        return $response;
    }
}
