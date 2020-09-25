<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
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

    public function findAll() {
        return $this->createQueryBuilder('e')
            ->select('e', 'u', 's')
            ->join('e.status', 's')
            ->join('e.organizer', 'u')
            ->getQuery()
            ->getResult();
    }

//    Filtre l'affichage des events avec l'incrémentation d'une requête SQL en fonction des critères séléctionnés par l'utilisateur
    public function filterEvents($criteria, $userId)
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e')
            ->addSelect('u')
            ->join('e.attendees', 'u');

        if ($criteria['organizer']) {
            $qb
                ->addSelect('user')
                ->join('e.organizer', 'user')
                ->orWhere('user.id = :userId')
                ->setParameter('userId', $userId);
        }

        if ($criteria['registered']) {
            $qb
                ->orWhere('u.id = :userId')
                ->setParameter('userId', $userId);
        }

        if ($criteria['notRegistered']) {
            $qb
                ->orWhere('u.id != :userId')
                ->setParameter('userId', $userId);
        }

        if ($criteria['over']) {
            $qb
                ->addSelect('s')
                ->join('e.status', 's')
                ->orWhere('s.label = :label')
                ->setParameter('label', 'Fermée');
        }

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

    public function findAttendees($idEvent)
    {
        return $this->createQueryBuilder('e')
            ->addSelect('u')
            ->join('e.attendees', 'u')
            ->where('e.id = :idEvent')
            ->setParameter('idEvent', $idEvent)
            ->getQuery()
            ->getResult();
    }
}
