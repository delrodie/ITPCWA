<?php

namespace App\Repository;

use App\Entity\FrRessource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FrRessource>
 *
 * @method FrRessource|null find($id, $lockMode = null, $lockVersion = null)
 * @method FrRessource|null findOneBy(array $criteria, array $orderBy = null)
 * @method FrRessource[]    findAll()
 * @method FrRessource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FrRessourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FrRessource::class);
    }

    public function save(FrRessource $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FrRessource $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findListActif()
    {
        return $this->createQueryBuilder('fr')
            ->where('fr.pageIndex is not null')
            ->orderBy('fr.id', "DESC")
            ->getQuery()->getResult()
            ;
    }

    public function findListInactif()
    {
        return $this->createQueryBuilder('fr')
            ->where('fr.pageIndex is null')
            ->getQuery()->getResult()
            ;
    }

//    /**
//     * @return FrRessource[] Returns an array of FrRessource objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FrRessource
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
