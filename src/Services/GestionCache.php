<?php

namespace App\Services;

use App\Repository\FrInfoRepository;
use App\Repository\SlideRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class GestionCache
{
    public function __construct(
        private CacheInterface $cache, private SlideRepository $slideRepository,
        private FrInfoRepository $frInfoRepository
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
        if ($delete) return $this->cache->delete('slides');

        return $this->cache->get('slides', function (ItemInterface $item){
            $item->expiresAfter(604800); // Une semaine de mise en cache
            return $this->slideRepository->findBy(['statut' => true], ['id'=>"DESC"], 3);
        });
    }

    /**
     * Mise en cache des message en francais
     *
     * @throws InvalidArgumentException
     */
    public function cacheFrMessages(bool $delete=false)
    {
        if ($delete) return $this->cache->delete('frInfos');

        return $this->cache->get('frInfos', function (ItemInterface $item){
            $item->expiresAfter(604800);
            return $this->frInfoRepository->findListActif();
        });
    }
}