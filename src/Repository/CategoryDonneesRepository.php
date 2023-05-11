<?php

namespace App\Repository;

use App\Entity\CategoryDonnees;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategoryDonnees|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryDonnees|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryDonnees[]    findAll()
 * @method CategoryDonnees[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryDonneesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryDonnees::class);
    }

    // /**
    //  * @return CategoryDonnees[] Returns an array of CategoryDonnees objects
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
    public function findOneBySomeField($value): ?CategoryDonnees
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
