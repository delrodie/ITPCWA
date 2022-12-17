<?php

namespace App\Repository;

use App\Entity\FrInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FrInfo>
 *
 * @method FrInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method FrInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method FrInfo[]    findAll()
 * @method FrInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FrInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FrInfo::class);
    }

    public function save(FrInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FrInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Liste des messages actifs
     *
     * @return float|int|mixed|string
     */
    public function findListActif()
    {
        return $this->createQueryBuilder('fr')
            ->where('fr.statut = :statut')
            ->andWhere(':date BETWEEN fr.debut AND fr.fin')
            ->setParameters([
                'statut' => true,
                'date' => date('Y-m-d 00:00:00')
            ])
            ->getQuery()->getResult()
            ;
    }

//    /**
//     * @return FrInfo[] Returns an array of FrInfo objects
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

//    public function findOneBySomeField($value): ?FrInfo
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
