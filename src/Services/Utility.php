<?php

namespace App\Services;

use App\Entity\Traduction;
use App\Entity\Visiteur;
use App\Form\EnProjetType;
use App\Repository\AlbumRepository;
use App\Repository\EnActualiteRepository;
use App\Repository\EnAlbumRepository;
use App\Repository\EnBienvenueRepository;
use App\Repository\EnJobRepository;
use App\Repository\EnPresentationRepository;
use App\Repository\EnProjetRepository;
use App\Repository\EnRessourceRepository;
use App\Repository\EnTypeRepository;
use App\Repository\FrActualiteRepository;
use App\Repository\FrBienvenueRepository;
use App\Repository\FrJobRepository;
use App\Repository\FrPresentationRepository;
use App\Repository\FrProjetRepository;
use App\Repository\FrRessourceRepository;
use App\Repository\FrTypeRepository;
use App\Repository\SlideRepository;
use App\Repository\TraductionRepository;
use App\Repository\VisiteurRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\AsciiSlugger;

class Utility
{
    public function __construct(
        private SlideRepository $slideRepository, private RequestStack $requestStack,
        private VisiteurRepository $visiteurRepository, private FrTypeRepository $frTypeRepository,
        private EnTypeRepository $enTypeRepository, private TraductionRepository $traductionRepository,
        private EntityManagerInterface $entityManager, private FrPresentationRepository $frPresentationRepository,
        private EnPresentationRepository $enPresentationRepository, private FrActualiteRepository $frActualiteRepository,
        private EnActualiteRepository $enActualiteRepository, private FrProjetRepository $frProjetRepository,
        private EnProjetRepository $enProjetRepository, private FrRessourceRepository $frRessourceRepository,
        private EnRessourceRepository $enRessourceRepository, private FrJobRepository $frJobRepository,
        private EnJobRepository $enJobRepository, private AlbumRepository $albumRepository,
        private EnAlbumRepository $enAlbumRepository, private FrBienvenueRepository $frBienvenueRepository,
        private EnBienvenueRepository $enBienvenueRepository
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
            $contenu = substr(strip_tags($entity->getContenu()),0,155);
            $entity->setResume($contenu);
        }

        $entity->setSlug($slug);

        return $entity;
    }

    public function pagePlusVisited()
    {
        return $this->visiteurRepository->findMostPageVisit();
        //dd();
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
        //$first = date('w') + 7;
        $debut = date('d') - (int) date('w') + 1;
        $fin = date('d') - (int) date('w') + 7; //dd($first);

        return  [
            'debut' => date('Y-m-').$debut,
            'fin' => date('Y-m-').$fin
        ];
    }

    public function visteurLogs()
    {
        $path = $this->requestStack->getCurrentRequest()->server->get('DOCUMENT_ROOT').'/logs.json';
        if (!file_exists($path)) return [];

        $datas = json_decode(file_get_contents('logs.json'), true);

        arsort($datas);
        $list=[];$i=0;
        foreach ($datas as $data){ //dd($data);
            $list[$i++] = [
                'id' => $i,
                'ip' => $data['ip'],
                'page' => '<a href="'.$data['url'].'" target="_blank">'.$this->page_titre($data['page']).'</a>',
                'url' => $data['url'],
                'session' => $data['session'],
                'created_at' => $data['createdAt']
            ];
        }
        //dd($list);
        return $list;
    }

    protected function page_titre(int $page): string
    {
        return match ($page){
            1 => "Page d'accueil principal",
            2 => "Page d'accueil version francaise",
            3 => "Page d'accueil version anglaise",
            4 => "Liste des actualités version francaise",
            5 => "Liste des actualités version anglaise",
            6 => "Article de l'actualité en version francaise",
            7 => "Article de l'actualité en version anglaise",
            8 => "Version francaise de la liste des projets",
            9 => "Version anglaise de la liste des projets",
            10 => "Version francaise d'un article des projets",
            11 => "Version anglaise d'un article des projets",
            12 => "Version francaise de la liste des ressources",
            13 => "Version anglaise de la liste des ressources",
            14 => "version francaise de la liste des recruitements",
            15 => "Version anglaise de la liste des recruitements",
            16 => "Version francaise d'un article de recruitement",
            17 => "Version anglaise d'un article de recruitement",
        };
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function traductionSave($fr, $en, string $route): bool
    {
        // recuperation de la liste des traductions
        $lastTraduction = $this->traductionRepository->findOneBy([],['id'=>"DESC"]);
        if (!$lastTraduction) $id=1;
        else $id = $lastTraduction->getId();

        $traduction = new Traduction();
        $traduction->setPage($id);
        $traduction->setRoute($route);
        $this->entityManager->persist($traduction);

        $fr->setPageIndex($id);
        $en->setPageIndex($id);

        $this->entityManager->flush();

        return true;
    }

    /**
     * Suppression de la ligne suppression et de la correspondance en français
     *
     * @param $en
     * @param string $route
     * @return bool
     */
    public function traductionDelete($en, string $route): bool
    {
        $fr = $this->traductionRoute($route, $en->getPageIndex());
        $traduction = $this->traductionRepository->findOneBy(['page' => $en->getPageIndex()]);
        if ($fr and $traduction) {
            $fr->setPageIndex(null);
            $this->entityManager->flush();

            $this->traductionRepository->remove($traduction, true);

            return true;
        }

        return false;

    }

    /**
     * Recherche du type d'entité concerné par la traduction pour suppression
     *
     */
    public function traductionRoute($route, int $pageIndex)
    {
        return match ($route){
            'type' => $this->frTypeRepository->findOneBy(['pageIndex' => $pageIndex]),
            'actualite' => $this->frActualiteRepository->findOneBy(['pageIndex' => $pageIndex]),
            'projet' => $this->frProjetRepository->findOneBy(['pageIndex' => $pageIndex]),
            'job' => $this->frJobRepository->findOneBy(['pageIndex' => $pageIndex]),
            'album' => $this->albumRepository->findOneBy(['pageIndex' => $pageIndex]),
        };
    }

    /**
     * Recherche de l'article francais pour afficher dans le select
     *
     * @param $route
     * @param int $pageIndex
     * @return array
     */
    public function traductionSelect(int $pageIndex, $route): array
    {
        $fr = $this->traductionRoute($route, $pageIndex);
        return [
            'pageIndex' => $fr->getPageIndex(),
            'titre' => $fr->getTitre()
        ];
    }

    public function getReference($entity, string $lang)
    {
        if ($lang === 'fr')
            $lastReference = $this->frRessourceRepository->findOneBy([],['id'=>"DESC"]);
        else
            $lastReference = $this->enRessourceRepository->findOneBy([],['id'=>"DESC"]);

        //$date = date('ym');
        if (!$lastReference) $ref = $this->reference(1);
        else $ref = $this->reference($lastReference->getId());

        return $entity->setReference($ref);
    }

    public function referenceJob($entity, string $lang)
    {
        if ($lang === 'fr') {
            $lastReference = $this->frJobRepository->findOneBy([], ['id' => "DESC"]);
            $lettre = 'F';
        }
        else {
            $lastReference = $this->enJobRepository->findOneBy([],['id'=>'DESC']);
            $lettre = 'E';
        }

        if (!$lastReference) $ref = $lettre.$this->reference(1);
        else $ref = $lettre.$this->reference((int) $lastReference->getId());

        return $entity->setReference($ref);
    }

    protected function reference(int $id)
    {
        $date = date('ym');
        if ($id < 10) $res = '00'.$id;
        elseif ($id < 100) $res = '0'.$id;
        else $res = $id;

        return $date.$res;
    }

    public function postulerRequest($form)
    {
        $mediaCV = $form->get('mediaCV')->getData();
        $mediaLettre = $form->get('mediaLettre')->getData();


    }

}