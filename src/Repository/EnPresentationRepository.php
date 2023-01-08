<?php

namespace App\Repository;

use App\Entity\EnPresentation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnPresentation>
 *
 * @method EnPresentation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnPresentation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnPresentation[]    findAll()
 * @method EnPresentation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnPresentationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnPresentation::class);
    }

    public function save(EnPresentation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EnPresentation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByType($slug)
    {
        return $this->createQueryBuilder('ep')
            ->addSelect('t')
            ->leftJoin('ep.type', 't')
            ->where('t.slug = :type')
            //->setMaxResults(1)
            ->setParameter('type', $slug)
            ->getQuery()->getOneOrNullResult()
            ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByTerm($string)
    {
        return $this->createQueryBuilder('ep')
            ->addSelect('t')
            ->leftJoin('ep.type', 't')
            ->where('t.titre LIKE :string')
            ->andWhere('t.pageIndex IS NOT NULL')
            ->setParameter('string', '%'.$string.'%')
            //->setMaxResults(1)
            ->getQuery()->getOneOrNullResult()
            ;
    }

//    /**
//     * @return EnPresentation[] Returns an array of EnPresentation objects
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

//    public function findOneBySomeField($value): ?EnPresentation
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
