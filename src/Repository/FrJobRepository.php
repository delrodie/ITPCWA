<?php

namespace App\Repository;

use App\Entity\FrJob;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FrJob>
 *
 * @method FrJob|null find($id, $lockMode = null, $lockVersion = null)
 * @method FrJob|null findOneBy(array $criteria, array $orderBy = null)
 * @method FrJob[]    findAll()
 * @method FrJob[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FrJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FrJob::class);
    }

    public function save(FrJob $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FrJob $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findListActif()
    {
        return $this->createQueryBuilder('fj')
            ->where('fj.pageIndex is null')
            ->getQuery()->getResult()
            ;
    }

//    /**
//     * @return FrJob[] Returns an array of FrJob objects
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

//    public function findOneBySomeField($value): ?FrJob
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
