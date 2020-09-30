<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\CampusSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Campus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Campus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Campus[]    findAll()
 * @method Campus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campus::class);
    }

    /**
     * @param $search
     * @return Query
     */
   public function findCampusByName(CampusSearch $search)
   {
       $query = $this->findAllCampuses();

       if($search->getName())
       {
           $query = $query
               ->andWhere('p.name LIKE :val')
               ->setParameter('val', '%'.$search->getName().'%');
       }
       return $query->getQuery()
           ->getResult();
   }

    public function findAllCampuses(): QueryBuilder
    {
        return $this->createQueryBuilder('p');
    }

    public function getAllCampuses()
    {
        $campusArray = [];
        $campuses = $this->findAll();

        foreach ($campuses as $campus){
            $statusArray[$campus->getName()] = $campus;
        }
        return $statusArray;
    }
}
