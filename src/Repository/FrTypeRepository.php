<?php

namespace App\Repository;

use App\Entity\FrType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FrType>
 *
 * @method FrType|null find($id, $lockMode = null, $lockVersion = null)
 * @method FrType|null findOneBy(array $criteria, array $orderBy = null)
 * @method FrType[]    findAll()
 * @method FrType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FrTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FrType::class);
    }

    public function save(FrType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FrType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Liste des types non associés à la traduction anglaise
     *
     * @return float|int|mixed|string
     */
    public function findListInactif(): mixed
    {
        return $this->createQueryBuilder('ft')
            ->where('ft.pageIndex is null')
            ->orderBy('ft.titre', 'ASC')
            //->setParameter('page', false)
            ->getQuery()->getResult()
            ;
    }

    public function findListActif()
    {
        return $this->createQueryBuilder('ft')
            ->where('ft.pageIndex is not null')
            ->getQuery()->getResult()
            ;
    }

//    /**
//     * @return FrType[] Returns an array of FrType objects
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

//    public function findOneBySomeField($value): ?FrType
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
