<?php

namespace App\Repository;

use App\Entity\EnEquipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnEquipe>
 *
 * @method EnEquipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnEquipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnEquipe[]    findAll()
 * @method EnEquipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnEquipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnEquipe::class);
    }

    public function save(EnEquipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EnEquipe $entity, bool $flush = false): void
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
//     * @return EnEquipe[] Returns an array of EnEquipe objects
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

//    public function findOneBySomeField($value): ?EnEquipe
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
