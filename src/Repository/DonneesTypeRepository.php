<?php

namespace App\Repository;

use App\Entity\DonneesType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DonneesType|null find($id, $lockMode = null, $lockVersion = null)
 * @method DonneesType|null findOneBy(array $criteria, array $orderBy = null)
 * @method DonneesType[]    findAll()
 * @method DonneesType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonneesTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DonneesType::class);
    }

    // /**
    //  * @return DonneesType[] Returns an array of DonneesType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DonneesType
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
