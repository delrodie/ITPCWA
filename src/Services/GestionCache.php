<?php

namespace App\Services;

use App\Repository\EnInfoRepository;
use App\Repository\EnPresentationRepository;
use App\Repository\EnTypeRepository;
use App\Repository\FrActualiteRepository;
use App\Repository\FrInfoRepository;
use App\Repository\FrPresentationRepository;
use App\Repository\FrTypeRepository;
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
        private FrActualiteRepository $frActualiteRepository
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
}