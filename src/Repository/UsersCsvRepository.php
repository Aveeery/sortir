<?php

namespace App\Repository;

use App\Entity\UsersCsv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UsersCsv|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsersCsv|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsersCsv[]    findAll()
 * @method UsersCsv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersCsvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsersCsv::class);
    }

    // /**
    //  * @return UsersCsv[] Returns an array of UsersCsv objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UsersCsv
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
