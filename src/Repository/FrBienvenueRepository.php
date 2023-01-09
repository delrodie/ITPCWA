<?php

namespace App\Repository;

use App\Entity\FrBienvenue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FrBienvenue>
 *
 * @method FrBienvenue|null find($id, $lockMode = null, $lockVersion = null)
 * @method FrBienvenue|null findOneBy(array $criteria, array $orderBy = null)
 * @method FrBienvenue[]    findAll()
 * @method FrBienvenue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FrBienvenueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FrBienvenue::class);
    }

    public function save(FrBienvenue $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FrBienvenue $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneOrNull()
    {
        return $this->createQueryBuilder('fb')
            ->orderBy('fb.id', 'DESC')
            ->getQuery()->getOneOrNullResult()
            ;
    }

//    /**
//     * @return FrBienvenue[] Returns an array of FrBienvenue objects
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

//    public function findOneBySomeField($value): ?FrBienvenue
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
