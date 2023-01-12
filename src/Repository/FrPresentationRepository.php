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
     * @param $slug
     * @return mixed|null
     */
    public function findByType($slug): mixed
    {
        $query =  $this->createQueryBuilder('fp')
            ->addSelect('t')
            ->leftJoin('fp.type', 't')
            ->where('t.slug = :type')
            ->setMaxResults(1)
            ->setParameter('type', $slug)
            ->getQuery()->getResult()
            ;
        if (count($query) > 0) return $query[0];

        return null;
    }

    /**
     * @param $string
     * @return array|mixed
     */
    public function findByTerm($string): mixed
    { //dd($string);
        $query = $this->createQueryBuilder('fp')
            ->addSelect('t')
            ->leftJoin('fp.type', 't')
            ->where('t.titre LIKE :string')
            ->andWhere('t.pageIndex IS NOT NULL')
            ->setParameter('string', '%'.$string.'%')
            ->setMaxResults(1)
            ->getQuery()->getResult()
            ;

        if ($query) return $query[0];

        return [];
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
