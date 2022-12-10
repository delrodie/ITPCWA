<?php

namespace App\Services;

use App\Repository\SlideRepository;
use Symfony\Component\String\Slugger\AsciiSlugger;

class Utility
{
    public function __construct(
        private SlideRepository $slideRepository,
    )
    {
    }

    /**
     * Fonction de generation de slug et de résumé
     *
     * @param $entity // Transmission de l'entité concernée
     * @param string $entityName // Le nom de l'entité
     * @param boolean|null $resume // Valeur est à null sila genération concerne uniquement le slug
     * @return false|mixed
     */
    public function slug($entity, string $entityName, bool $resume=null): mixed
    {
        $repository = $entityName.'Repository';

        // Generation du slide
        $slugify = new AsciiSlugger();
        $slug = $slugify->slug(strtolower($entity->getTitre()));

        // Vefirication de la non existence de ce slug dans la base de données
        $verif = $this->$repository->findOneBy(['slug' => $slug]);
        if ($verif) return false;

        // Generation du resume
        if ($resume){
            $contenu = substr(strip_tags($resume),0,155);
            $entity->setResume($contenu);
        }

        $entity->setSlug($slug);

        return $entity;
    }
}