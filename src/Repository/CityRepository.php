<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\CitySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    /**
     * @param $search
     * @return Query
     */
    public function findCityByName(CitySearch $search)
    {
        $query = $this->findAllCities();

            if ($search->getName())
            {
                $query = $query
                ->andWhere('p.name LIKE :val')
                ->setParameter('val', '%'.$search->getName().'%');
            }

        ;
        return $query->getQuery()
            ->getResult();
    }

    public function findAllCities(): \Doctrine\ORM\QueryBuilder
    {
        return $this->createQueryBuilder('p');
    }


    /*
    public function findOneBySomeField($value): ?City
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
