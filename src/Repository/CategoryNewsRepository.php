<?php

namespace App\Repository;

use App\Entity\CategoryNews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategoryNews|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryNews|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryNews[]    findAll()
 * @method CategoryNews[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryNewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryNews::class);
    }

    // /**
    //  * @return CategoryNews[] Returns an array of CategoryNews objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CategoryNews
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
