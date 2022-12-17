<?php

namespace App\Services;

use App\Repository\FrInfoRepository;

class AllRepository
{
    public function __construct(
        private FrInfoRepository $frInfoRepository
    )
    {
    }

    public function frMessageActif()
    {
        return $this->frInfoRepository->findListActif();
    }
}