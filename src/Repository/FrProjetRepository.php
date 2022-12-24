<?php

namespace App\Repository;

use App\Entity\FrProjet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FrProjet>
 *
 * @method FrProjet|null find($id, $lockMode = null, $lockVersion = null)
 * @method FrProjet|null findOneBy(array $criteria, array $orderBy = null)
 * @method FrProjet[]    findAll()
 * @method FrProjet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FrProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FrProjet::class);
    }

    public function save(FrProjet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FrProjet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findListInactif()
    {
        return $this->createQueryBuilder('fp')
            ->where('fp.pageIndex is null')
            ->getQuery()->getResult()
            ;
    }

//    /**
//     * @return FrProjet[] Returns an array of FrProjet objects
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

//    public function findOneBySomeField($value): ?FrProjet
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
