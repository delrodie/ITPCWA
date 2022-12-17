<?php

namespace App\Services;

use App\Entity\Visiteur;
use App\Repository\SlideRepository;
use App\Repository\VisiteurRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\AsciiSlugger;

class Utility
{
    public function __construct(
        private SlideRepository $slideRepository, private RequestStack $requestStack,
        private VisiteurRepository $visiteurRepository
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

    /**
     * Instanciation de la table visiteur
     *
     * @return bool
     */
    public function visiteur(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        //$request->getSession()->clear();

        if (!$request->getSession()->get('_visiteur')) {
            $request->getSession()->set('_visiteur', uniqid(12));

            $visiteur = new Visiteur();
            $visiteur->setIp($request->getClientIp());
            $visiteur->setPage($this->route_page($request->get('_route')));
            $visiteur->setUrl($request->getUri());
            $visiteur->setSession($request->getSession()->get('_visiteur'));

            $this->visiteurRepository->save($visiteur, true);
        }

        return true;
    }

    public function pagePlusVisited()
    {
        return $this->visiteurRepository->findMostPageVisit();
        //dd();
    }

    /**
     * Generation du numero de la page frontend
     *
     * @param string $route
     * @return int
     */
    private function route_page(string $route): int
    {
        $resulat = match ($route){
            'app_home' => 1,
            'app_francais' => 2,
        };

        return $resulat;
    }

    /**
     * Tableau des visiteurs
     *
     * @return array
     */
    public function vistiteurList(): array
    {
        return  [
            'total' => count($this->visiteurRepository->findAll()),
            'mois' => count($this->visiteurRepository->findList(date('Y-m-01'), date('Y-m-31'))),
            'semaine' => count($this->visiteurRepository->findList($this->semaine()['debut'], $this->semaine()['fin'])),
            'jour' => count($this->visiteurRepository->findList(date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')))
        ];


    }

    private function semaine()
    {
        $debut = date('d') - date('w') + 1;
        $fin = date('d') - date('w') + 7;

        return  [
            'debut' => date('Y-m-').$debut,
            'fin' => date('Y-m-').$fin
        ];
    }
}