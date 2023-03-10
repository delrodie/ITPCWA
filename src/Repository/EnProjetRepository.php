<?php

namespace App\Repository;

use App\Entity\EnProjet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnProjet>
 *
 * @method EnProjet|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnProjet|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnProjet[]    findAll()
 * @method EnProjet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnProjet::class);
    }

    public function save(EnProjet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EnProjet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findListActif()
    {
        return $this->createQueryBuilder('ep')
            ->where('ep.pageIndex is not null')
            ->orderBy('ep.id', 'DESC')
            ->getQuery()->getResult()
            ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findLastActif()
    {
        $query = $this->createQueryBuilder('ep')
            ->where('ep.pageIndex IS NOT NULL')
            ->orderBy('ep.id', "DESC")
            ->setMaxResults(1)
            ->getQuery()->getResult()
            ;
        if ($query) {
            return $query[0];
        }

        return null;
    }

//    /**
//     * @return EnProjet[] Returns an array of EnProjet objects
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

//    public function findOneBySomeField($value): ?EnProjet
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
