<?php

namespace App\Services;

use App\Repository\EnInfoRepository;
use App\Repository\FrInfoRepository;
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
        private FrTypeRepository $frTypeRepository
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
            return $this->frTypeRepository->findAll();
        });
    }
}