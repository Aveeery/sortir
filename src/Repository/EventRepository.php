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

    public function findAll()
    {
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
                ->andWhere('user.id = :userId')
                ->setParameter('userId', $userId);
        }

        //Si l'utilisateur séléctionne toutes les sorties terminées, on compare la date de l'evenement "startDate" avec la date du jour
        if ($criteria['over']) {
                 $now = new \DateTime("now");
                 $now->add(new \DateInterval('PT2H'));
                 $qb
                ->andWhere('e.startDate < :date')
                ->setParameter('date', $now);
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
        $events = $query->getResult();

        //Si le filtre "Je ne suis pas inscrit" est activé, on boucle sur chaque participant de chaque event, si l'utilisateur est à l'intérieur, on sort l'evenement en question du tableau $event
        if ($criteria['registeredOrNot'] == 'registered') {
            $events = array_filter($events, function (Event $event) use ($userId) {
                $attendees = $event->getAttendees();
                foreach ($attendees as $attendee) {
                    if ($attendee->getId() == $userId) {
                        return true;
                    }
                }
                return false;
            });
        }

        if ($criteria['registeredOrNot'] == 'notRegistered') {
            $events = array_filter($events, function (Event $event) use ($userId) {
                $attendees = $event->getAttendees();
                foreach ($attendees as $attendee) {
                    if ($attendee->getId() == $userId) {
                        return false;
                    }
                }
                return true;
            });
        }

        return $events;
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
