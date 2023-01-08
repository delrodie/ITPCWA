<?php

namespace App\Repository;

use App\Entity\FrPresentation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FrPresentation>
 *
 * @method FrPresentation|null find($id, $lockMode = null, $lockVersion = null)
 * @method FrPresentation|null findOneBy(array $criteria, array $orderBy = null)
 * @method FrPresentation[]    findAll()
 * @method FrPresentation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FrPresentationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FrPresentation::class);
    }

    public function save(FrPresentation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FrPresentation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findByType($slug)
    {
        return $this->createQueryBuilder('fp')
            ->addSelect('t')
            ->leftJoin('fp.type', 't')
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
    { //dd($string);
        return $this->createQueryBuilder('fp')
            ->addSelect('t')
            ->leftJoin('fp.type', 't')
            ->where('t.titre LIKE :string')
            ->setParameter('string', '%'.$string.'%')
            //->setMaxResults(1)
            ->getQuery()->getOneOrNullResult()
            ;
    }
//    /**
//     * @return FrPresentation[] Returns an array of FrPresentation objects
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

//    public function findOneBySomeField($value): ?FrPresentation
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
