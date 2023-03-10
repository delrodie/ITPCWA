<?php

namespace App\Repository;

use App\Entity\EnJob;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnJob>
 *
 * @method EnJob|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnJob|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnJob[]    findAll()
 * @method EnJob[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnJob::class);
    }

    public function save(EnJob $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EnJob $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findListActif()
    {
        return $this->createQueryBuilder('ej')
            ->where('ej.pageIndex is not null')
            ->orderBy('ej.fin', 'DESC')
            ->getQuery()->getResult()
            ;
    }

//    /**
//     * @return EnJob[] Returns an array of EnJob objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EnJob
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
