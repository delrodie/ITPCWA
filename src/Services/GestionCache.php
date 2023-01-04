<?php

namespace App\Services;

use App\Repository\AlbumRepository;
use App\Repository\EnActualiteRepository;
use App\Repository\EnAlbumRepository;
use App\Repository\EnInfoRepository;
use App\Repository\EnJobRepository;
use App\Repository\EnPresentationRepository;
use App\Repository\EnProjetRepository;
use App\Repository\EnRessourceRepository;
use App\Repository\EnTypeRepository;
use App\Repository\FrActualiteRepository;
use App\Repository\FrInfoRepository;
use App\Repository\FrJobRepository;
use App\Repository\FrPresentationRepository;
use App\Repository\FrProjetRepository;
use App\Repository\FrRessourceRepository;
use App\Repository\FrTypeRepository;
use App\Repository\PhotoRepository;
use App\Repository\SlideRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class GestionCache
{
    public function __construct(
        private CacheInterface $cache, private SlideRepository $slideRepository,
        private FrInfoRepository $frInfoRepository, private  EnInfoRepository $enInfoRepository,
        private FrTypeRepository $frTypeRepository, private EnTypeRepository $enTypeRepository,
        private FrPresentationRepository $frPresentationRepository, private EnPresentationRepository $enPresentationRepository,
        private FrActualiteRepository $frActualiteRepository, private EnActualiteRepository $enActualiteRepository,
        private FrProjetRepository $frProjetRepository, private EnProjetRepository $enProjetRepository,
        private FrRessourceRepository $frRessourceRepository, private EnRessourceRepository $enRessourceRepository,
        private FrJobRepository $frJobRepository, private EnJobRepository $enJobRepository,
        private AlbumRepository $albumRepository, private EnAlbumRepository $enAlbumRepository,
        private PhotoRepository $photoRepository
    )
    {
    }

    /**
     * Mise en cache des slides ou les suppressions
     *
     * @throws InvalidArgumentException
     */
    public function cacheSlides(bool $delete=false)
    {

        if ($delete) $this->cache->delete('slides');

        return $this->cache->get('slides', function (ItemInterface $item){
            $item->expiresAfter(604800); // Une semaine de mise en cache
            return $this->slideRepository->findBy(['statut' => true], ['id'=>"DESC"]);
        });
    }

    /**
     * Mise en cache des message en francais
     *
     * @throws InvalidArgumentException
     */
    public function cacheFrMessages(bool $delete=false)
    {
        if ($delete) $this->cache->delete('frInfos');

        return $this->cache->get('frInfos', function (ItemInterface $item){
            $item->expiresAfter(604800);
            return $this->frInfoRepository->findListActif();
        });
    }

    /**
     * Mise en cache des messages en Anglais
     *
     * @throws InvalidArgumentException
     */
    public function cacheEnMessage(bool $delete=false)
    {
        if ($delete) $this->cache->delete('enInfos');

        return $this->cache->get('enInfos', function (ItemInterface $item){
            $item->expiresAfter(604800);
            return $this->enInfoRepository->findListActif();
        });
    }

    /**
     * @throws InvalidArgumentException
     */
    public function cacheFrType(bool $delete=false)
    {
        if ($delete) $this->cache->delete('frType');

        return $this->cache->get('frType', function (ItemInterface $item){
            $item->expiresAfter(604800);
            return $this->frTypeRepository->findListActif();
        });
    }

    /**
     * @throws InvalidArgumentException
     */
    public function cacheEnType(bool $delete=false)
    {
        if ($delete) $this->cache->delete('enType');

        return $this->cache->get('enType', function (ItemInterface $item){
            $item->expiresAfter(6048000);
            return $this->enTypeRepository->findListActif();
        });
    }

    /**
     * @throws InvalidArgumentException
     */
    public function cacheFrPresentation($slug, bool $delete=false)
    {
        if($delete) $this->cache->delete($slug);

        return $this->cache->get($slug, function (ItemInterface $item) use ($slug){
            $item->expiresAfter(6048000);
            $presentation =$this->frPresentationRepository->findByType($slug) ; //dd($presentation);
            if ($presentation){
                $enType = $this->enTypeRepository->findOneBy(['pageIndex' => $presentation->getType()->getPageIndex()]);

                $resultat = [
                    'id' => $presentation->getId(),
                    'titre' => $presentation->getTitre(),
                    'resume' => $presentation->getResume(),
                    'contenu' => $presentation->getContenu(),
                    'media' => $presentation->getMedia(),
                    'slug' => $presentation->getSlug(),
                    'tags' => $presentation->getTags(),
                    'createdAt' => $presentation->getCreatedAt(),
                    'updatedAt' => $presentation->getUpdatedAt(),
                    'type_id' => $presentation->getType()->getId(),
                    'type_titre' => $presentation->getType()->getTitre(),
                    'type_pageIndex' => $presentation->getType()->getPageIndex(),
                    'type_slug' => $presentation->getType()->getSlug(),
                    'traduction' => $enType->getSlug()
                ];
            }else{
                $resultat = [];
            }

            return $resultat;

        });
    }

    /**
     * @throws InvalidArgumentException
     */
    public function cacheEnPresentation($slug, bool $delete=false)
    {
        if ($delete) $this->cache->delete($slug);

        return $this->cache->get($slug, function (ItemInterface $item) use ($slug){
            $item->expiresAfter(6048000);
            $presentation =$this->enPresentationRepository->findByType($slug) ; //dd($presentation);
            if ($presentation){
                $frType = $this->frTypeRepository->findOneBy(['pageIndex' => $presentation->getType()->getPageIndex()]);

                $resultat = [
                    'id' => $presentation->getId(),
                    'titre' => $presentation->getTitre(),
                    'resume' => $presentation->getResume(),
                    'contenu' => $presentation->getContenu(),
                    'media' => $presentation->getMedia(),
                    'slug' => $presentation->getSlug(),
                    'tags' => $presentation->getTags(),
                    'createdAt' => $presentation->getCreatedAt(),
                    'updatedAt' => $presentation->getUpdatedAt(),
                    'type_id' => $presentation->getType()->getId(),
                    'type_titre' => $presentation->getType()->getTitre(),
                    'type_pageIndex' => $presentation->getType()->getPageIndex(),
                    'type_slug' => $presentation->getType()->getSlug(),
                    'traduction' => $frType->getSlug(),
                ];
            }else{
                $resultat = [];
            }

            return $resultat;

        });
    }

    public function cacheFrActualites(bool $delete=false)
    {
        if ($delete) $this->cache->delete('frActualites');

        return $this->cache->get('frActualites', function (ItemInterface $item){
            $item->expiresAfter(6048000);
            return $this->frActualiteRepository->findListActif();
        });
    }


    public function cacheEnActualites(bool $delete=false)
    {
        if ($delete) $this->cache->delete('enActualites');

        return $this->cache->get('enActualites', function (ItemInterface $item){
            $item->expiresAfter(6048000);
            return $this->enActualiteRepository->findListActif();
        });
    }


    public function cacheFrActualiteItem($slug, bool $delete=false)
    {
        if ($delete) $this->cache->delete($slug); //dd($slug);

        return $this->cache->get($slug, function (ItemInterface $item) use ($slug){
            $item->expiresAfter(6048000);
            $actualite = $this->frActualiteRepository->findOneBy(['slug' => $slug]);
            if ($actualite){
                $traduction = $this->enActualiteRepository->findOneBy(['pageIndex' => $actualite->getPageIndex()]);
                $other = $this->frActualiteRepository->findOther($slug);
                return [
                    'locale' => $actualite,
                    'traduction' => $traduction,
                    'others' => $other
                ];
            }else{
                return [];
            }
        });
    }

    public function cacheEnActualiteItem($slug, bool $delete=false)
    {
        if ($delete) $this->cache->delete($slug);

        return $this->cache->get($slug, function (ItemInterface $item) use ($slug){
            $item->expiresAfter(6048000);
            $actualite = $this->enActualiteRepository->findOneBy(['slug' => $slug]);
            if ($actualite){
                $traduction = $this->frActualiteRepository->findOneBy(['pageIndex' => $actualite->getPageIndex()]);
                $other = $this->enActualiteRepository->findOther($actualite->getSlug());
                return [
                    'locale' => $actualite,
                    'traduction' => $traduction,
                    'others' => $other
                ];
            }else{
                return [];
            }
        });
    }

    public function cacheFrProjet(bool $delete=false)
    {
        if ($delete) $this->cache->delete('frProjet');

        return $this->cache->get('frProjet', function (ItemInterface $item){
            $item->expiresAfter(6048000);
            return $this->frProjetRepository->findListActif();
        });
    }

    public function cacheEnProjet(bool $delete=false)
    {
        if ($delete) $this->cache->delete('enProjet');

        return $this->cache->get('enProjet', function (ItemInterface $item){
            $item->expiresAfter(6048000);
            return $this->enProjetRepository->findListActif();
        });
    }

    public function cacheFrProjetItem(string $slug, bool $delete=false)
    {
        if ($delete) $this->cache->delete($slug);

        return $this->cache->get($slug, function (ItemInterface $item) use ($slug){
            $item->expiresAfter(6048000);
            $projet = $this->frProjetRepository->findOneBy(['slug' => $slug]);
            if ($projet){
                $traduction = $this->enProjetRepository->findOneBy(['pageIndex' => $projet->getPageIndex()]);
                // les actions concernées par ce projet
                return [
                    'locale' => $projet,
                    'traduction' => $traduction,
                    'actions' => []
                ];
            }else
                return [];
        });
    }

    public function cacheEnProjetItem(string $slug, bool $delete=false)
    {
        if ($delete) $this->cache->delete($slug);

        return $this->cache->get($slug, function (ItemInterface $item) use ($slug){
            $item->expiresAfter(6048000);
            $projet = $this->enProjetRepository->findOneBy(['slug' => $slug]);
            if ($projet){
                $traduction = $this->frProjetRepository->findOneBy(['pageIndex' => $projet->getPageIndex()]);
                // Les actions de ce projet
                return [
                    'locale' => $projet,
                    'traduction' => $traduction,
                    'actions' => []
                ];
            }else
                return [];
        });
    }

    public function cacheFrRessource(bool $delete=false)
    {
        if ($delete) $this->cache->delete('frRessource');

        return $this->cache->get('frRessource', function (ItemInterface $item){
            $item->expiresAfter(6048000);
            return $this->frRessourceRepository->findBy([],['id'=>"DESC"]);
        });
    }

    public function cacheEnRessource(bool $delete=false)
    {
        if ($delete) $this->cache->delete('enRessource');

        return $this->cache->get('enRessource', function (ItemInterface $item){
            $item->expiresAfter(6048000);
            return $this->enRessourceRepository->findBy([],['id'=>"DESC"]);
        });
    }

    public function cacheJob(string $lang, bool $delete=false)
    {
        $cacheName = $lang.'Job';
        if ($delete) $this->cache->delete($cacheName);

        return $this->cache->get($cacheName, function (ItemInterface $item) use ($lang){
            $item->expiresAfter(6048000);

            if ($lang === 'fr') $jobs = $this->frJobRepository->findListActif();
            else $jobs = $this->enJobRepository->findListActif();

            return $jobs;
        });
    }

    public function cacheJobItem(string $lang, string $slug, bool $delete=false)
    {
        if ($delete) $this->cache->delete($slug);

        return $this->cache->get($slug, function (ItemInterface $item) use ($slug, $lang){
            $item->expiresAfter(6048000);
            if ($lang === 'fr') {
                $job = $this->frJobRepository->findOneBy(['slug' => $slug]);
                if ($job)
                    $traduction = $this->enJobRepository->findOneBy(['pageIndex'=>$job->getPageIndex()]);
                else
                    $traduction = [];
            }
            else {
                $job = $this->enJobRepository->findOneBy(['slug' => $slug]);
                if ($job)
                    $traduction = $this->frJobRepository->findOneBy(['pageIndex' => $job->getPageIndex()]);
                else
                    $traduction = [];
            }

            return [
                'locale' => $job,
                'traduction' => $traduction
            ];
        });
    }

    public function cacheAlbum(string $lang, bool $delete=false)
    {
        if ($delete) $this->cache->delete($lang);

        return $this->cache->get($lang, function (ItemInterface $item) use ($lang){
            $item->expiresAfter(6048000);

            if ($lang === 'fr') $album = $this->albumRepository->findListActif();
            else $album = $this->enAlbumRepository->findListActif();

            return $album;
        });
    }

    public function cacheAlbumItem(string $lang, string $slug, bool $delete=false)
    {
        if ($delete) $this->cache->delete($slug); //dd($lang);

        return $this->cache->get($slug, function (ItemInterface $item) use ($slug, $lang){
            $item->expiresAfter(6048000);
            if ($lang === 'fr') { //dd('francaise');
                $album = $this->albumRepository->findOneBy(['slug' => $slug]); //dd($album);
                if (!$album)
                    return ['locale' => [], 'traduction' => []];

                //dd($this->photoRepository->findByAlbumSlug($album->getSlug()));

                return [
                    'locale' => $album,
                    'traduction' => $this->enAlbumRepository->findOneBy(['pageIndex' => $album->getPageIndex()]),
                    'images' => $this->photoRepository->findByAlbumSlug($album->getSlug())
                ];
                //dd('fr');
            }else{ //dd('anglaise');
                $album = $this->enAlbumRepository->findOneBy(['slug' => $slug]); //dd($album);
                if (!$album)
                    return ['locale' => [], 'traduction' => [] ];

                $traduction = $this->albumRepository->findOneBy(['pageIndex' => $album->getPageIndex()]);
                return [
                    'locale' => $album,
                    'traduction' => $traduction,
                    'images' => $this->photoRepository->findByAlbumSlug($traduction->getSlug())
                ];

            }
        });
    }


}