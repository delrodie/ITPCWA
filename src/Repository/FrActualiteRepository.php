<?php

namespace App\Repository;

use App\Entity\FrActualite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FrActualite>
 *
 * @method FrActualite|null find($id, $lockMode = null, $lockVersion = null)
 * @method FrActualite|null findOneBy(array $criteria, array $orderBy = null)
 * @method FrActualite[]    findAll()
 * @method FrActualite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FrActualiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FrActualite::class);
    }

    public function save(FrActualite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FrActualite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findListInactif()
    {
        return $this->createQueryBuilder('fa')
            ->where('fa.pageIndex is null')
            ->getQuery()->getResult();
    }

    public function findListActif()
    {
        return $this->createQueryBuilder('fa')
            ->where('fa.pageIndex is not null')
            ->orderBy('fa.id', 'DESC')
            ->getQuery()->getResult()
            ;
    }

    public function findOther($slug)
    {
        return $this->createQueryBuilder('fa')
            ->where('fa.slug <> :slug')
            ->orderBy('fa.id', 'DESC')
            ->setParameter('slug', $slug)
            ->setMaxResults(2)
            ->getQuery()->getResult()
            ;
    }

//    /**
//     * @return FrActualite[] Returns an array of FrActualite objects
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

//    public function findOneBySomeField($value): ?FrActualite
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
