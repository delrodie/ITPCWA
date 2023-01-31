<?php

namespace App\Controller\Frontend;

use App\Repository\AlbumRepository;
use App\Repository\EnActualiteRepository;
use App\Repository\EnAlbumRepository;
use App\Repository\EnBienvenueRepository;
use App\Repository\EnEquipeRepository;
use App\Repository\EnJobRepository;
use App\Repository\EnPresentationRepository;
use App\Repository\EnProjetRepository;
use App\Repository\FrActualiteRepository;
use App\Repository\FrBienvenueRepository;
use App\Repository\FrEquipeRepository;
use App\Repository\FrJobRepository;
use App\Repository\FrPresentationRepository;
use App\Repository\FrProjetRepository;
use App\Services\GestionCache;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cache')]
class FrontendCacheController extends AbstractController
{
    public function __construct(
        private GestionCache $gestionCache, private Flasher $flasher, private FrPresentationRepository $frPresentationRepository,
        private EnProjetRepository $enProjetRepository, private FrActualiteRepository $frActualiteRepository,
        private EnActualiteRepository $enActualiteRepository, private FrProjetRepository $frProjetRepository,
        private EnPresentationRepository $enPresentationRepository, private FrJobRepository $frJobRepository,
        private EnJobRepository $enJobRepository, private AlbumRepository $albumRepository,
        private EnAlbumRepository $enAlbumRepository, private FrBienvenueRepository $frBienvenueRepository,
        private EnBienvenueRepository $enBienvenueRepository, private FrEquipeRepository $frEquipeRepository,
        private EnEquipeRepository $enEquipeRepository
    )
    {
    }

    #[Route('/', name: 'app_frontend_cache')]
    public function index(Request $request): Response
    {
        $this->gestionCache->cacheSlides(true);
        $langs = ['fr','en'];
        $itemPresentations = ['axe','presentation','intervention'];

        foreach ($langs as $lang){
            $this->gestionCache->cacheMessages($lang, true);
            $this->gestionCache->cacheType($lang, true);
            $this->gestionCache->cacheActualites($lang, true);
            $this->gestionCache->cacheProjets($lang, true);
            $this->gestionCache->cacheRessource($lang, true);
            $this->gestionCache->cacheJob($lang, true);
            $this->gestionCache->cacheAlbum($lang, true);
            $this->gestionCache->cacheBienvenue($lang, true);
            $this->gestionCache->cacheEquipe($lang, true);

            foreach ($itemPresentations as $str){
                $this->gestionCache->cacheItemPresentation($lang, $str, true);
            }

            if ($lang === 'fr'){
                $frPresentations = $this->frPresentationRepository->findAll();
                foreach ($frPresentations as $frPresentation){
                    $this->gestionCache->cacheFrPresentation($frPresentation->getSlug(), true);
                }

                $frActualites = $this->frActualiteRepository->findAll();
                foreach ($frActualites as $frActualite){
                    $this->gestionCache->cacheFrActualiteItem($frActualite->getSlug(), true);
                }
                $frProjets = $this->frProjetRepository->findAll();
                foreach ($frProjets as $projet){
                    $this->gestionCache->cacheFrProjetItem($projet->getSlug(), true);
                }

                $frJobs = $this->frJobRepository->findAll();
                foreach ($frJobs as $job){
                    $this->gestionCache->cacheJobItem($lang, $job->getSlug(), true);
                }

                $frAlbums = $this->albumRepository->findAll();
                foreach ($frAlbums as $album){
                    $this->gestionCache->cacheAlbumItem($lang, $album->getSlug(), true);
                }

                $frEquipe = $this->frEquipeRepository->findAll();
                foreach ($frEquipe as $equipe){
                    $this->gestionCache->cacheEquipeItem($lang, $equipe->getSlug(), true);
                }
            }else{
                $enPresentations = $this->enPresentationRepository->findAll();
                foreach ($enPresentations as $presentation){
                    $this->gestionCache->cacheItemPresentation($lang, $presentation->getSlug(), true);
                }

                $enActualites = $this->enActualiteRepository->findAll();
                foreach ($enActualites as $actualite){
                    $this->gestionCache->cacheEnActualiteItem($actualite->getSlug(), true);
                }

                $enProjets = $this->enProjetRepository->findAll();
                foreach ($enProjets as $projet){
                    $this->gestionCache->cacheEnProjetItem($projet->getSlug(), true);
                }

                $enJobs = $this->enJobRepository->findAll();
                foreach ($enJobs as $job){
                    $this->gestionCache->cacheJobItem($lang, $job->getSlug(), true);
                }

                $enAlbums = $this->enAlbumRepository->findAll();
                foreach ($enAlbums as $album){
                    $this->gestionCache->cacheAlbumItem($lang, $album->getSlug(), true);
                }

                $enEquipes = $this->enEquipeRepository->findAll();
                foreach ($enEquipes as $equipe){
                    $this->gestionCache->cacheEquipeItem($lang, $equipe->getSlug(), true);
                }
            }

        }

        $this->flasher
            ->create('sweetalert')
            ->addSuccess("Le cache BD a été réinitialisé avec succès:");

        $link = $request->server->get('HTTP_REFERER');
        if ($link) return $this->redirect($link);
        else return $this->redirectToRoute('app_home');


    }
}
