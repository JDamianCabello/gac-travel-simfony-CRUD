<?php

namespace App\Repository;

use App\Entity\StockHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StockHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockHistory[]    findAll()
 * @method StockHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockHistory::class);
    }

    // /**
    //  * @return StockHistory[] Returns an array of StockHistory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StockHistory
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
