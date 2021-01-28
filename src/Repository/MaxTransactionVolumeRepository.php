<?php

namespace App\Repository;

use App\Entity\MaxTransactionVolume;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MaxTransactionVolume|null find($id, $lockMode = null, $lockVersion = null)
 * @method MaxTransactionVolume|null findOneBy(array $criteria, array $orderBy = null)
 * @method MaxTransactionVolume[]    findAll()
 * @method MaxTransactionVolume[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaxTransactionVolumeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MaxTransactionVolume::class);
    }

    // /**
    //  * @return MaxTransactionVolume[] Returns an array of MaxTransactionVolume objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MaxTransactionVolume
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
