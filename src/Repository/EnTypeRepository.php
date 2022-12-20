<?php

namespace App\Repository;

use App\Entity\EnType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnType>
 *
 * @method EnType|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnType|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnType[]    findAll()
 * @method EnType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnType::class);
    }

    public function save(EnType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EnType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findListActif()
    {
        return $this->createQueryBuilder('fe')
            ->where('fe.pageIndex is not null')
            ->getQuery()->getResult()
            ;
    }

//    /**
//     * @return EnType[] Returns an array of EnType objects
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

//    public function findOneBySomeField($value): ?EnType
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
