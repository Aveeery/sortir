<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

//    Filtre l'affichage des events avec l'incrémentation d'une requête SQL en fonction des critères séléctionnés par l'utilisateur
    public function filterEvents($criteria, $userId)
    {

        $qb = $this->createQueryBuilder('e')
            ->select('e');


        if (strlen($criteria['name']) > 0) {
            $qb->andWhere('e.name = :name')
                ->setParameter('name', $criteria['name']);
        }

        if (strlen($criteria['campus']) > 0) {
            $qb
                ->addSelect('c')
                ->join('e.campus', 'c')
                ->andWhere('c.name = :campusName')
                ->setParameter('campusName', $criteria['campus']);
        }

        if ($criteria['organizer']) {
            $qb
                ->addSelect('u')
                ->join('e.organizer', 'u')
                ->andWhere('u.id = :userId')
                ->setParameter('userId', $userId);
        }

        if ($criteria['registered']) {
            $qb
                ->addSelect('us')
                ->join('e.attendees', 'us')
                ->andWhere('us.id = :userId')
                ->setParameter('userId', $userId);
        }

        if ($criteria['notRegistered']) {
            $qb
                ->addSelect('use')
                ->join('e.attendees', 'use')
                ->andWhere('use.id != :userId')
                ->setParameter('userId', $userId);
        }

        if ($criteria['firstDate']) {
            $qb
                ->andWhere('e.startDate > :firstDate')
                ->setParameter('firstDate', $criteria['firstDate']);
        }

        if ($criteria['secondDate']) {
            $qb
                ->andWhere('e.startDate < :secondDate')
                ->setParameter('secondDate', $criteria['secondDate']);
        }


        if ($criteria['over']) {
            $qb
                ->addSelect('s')
                ->join('e.status', 's')
                ->andWhere('s.label = :label')
                ->setParameter('label', 'Fermée');
        }


        $query = $qb->getQuery();
        return $query->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
