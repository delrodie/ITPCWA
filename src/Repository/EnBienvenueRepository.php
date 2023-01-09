<?php

namespace App\Repository;

use App\Entity\EnBienvenue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnBienvenue>
 *
 * @method EnBienvenue|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnBienvenue|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnBienvenue[]    findAll()
 * @method EnBienvenue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnBienvenueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnBienvenue::class);
    }

    public function save(EnBienvenue $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EnBienvenue $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneOrNull()
    {
        return $this->createQueryBuilder('eb')
            ->orderBy('eb.id', 'DESC')
            ->getQuery()->getOneOrNullResult()
            ;
    }

//    /**
//     * @return EnBienvenue[] Returns an array of EnBienvenue objects
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

//    public function findOneBySomeField($value): ?EnBienvenue
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
