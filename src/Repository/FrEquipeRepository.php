<?php

namespace App\Repository;

use App\Entity\FrEquipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FrEquipe>
 *
 * @method FrEquipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method FrEquipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method FrEquipe[]    findAll()
 * @method FrEquipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FrEquipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FrEquipe::class);
    }

    public function save(FrEquipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FrEquipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findListInactif()
    {
        return $this->createQueryBuilder('fe')
            ->where('fe.pageIndex is null')
            ->getQuery()->getResult()
            ;
    }

//    /**
//     * @return FrEquipe[] Returns an array of FrEquipe objects
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

//    public function findOneBySomeField($value): ?FrEquipe
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
