<?php

namespace App\Repository;

use App\Classe\Search;
use App\Entity\Document;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

	// TODO ajouter 'modifier le' àcoté de la date de création des entitées
    public function findByCategory($category)
    {
		return $this
			->createQueryBuilder('d')
			->join('d.categorydonnees', 'c')
			->andWhere('d.isActive = true')
			->andWhere('c.name = :category')
			->setParameter('category', $category)
			->orderBy('d.createdAt', 'DESC')
			->getQuery()
			->getResult();
    }

	public function findWithSearch($keyword, $categories, $from, $to) {
		$builder = $this
			->createQueryBuilder('d')
			->andWhere('d.isActive = true')
			->orderBy('d.createdAt', 'DESC');
		if (!empty($keyword)) {
			$builder
				->andWhere('d.title LIKE :keyword')
				->setParameter('keyword', '%'.$keyword.'%');
		}
		if (!empty($categories)) {
			$builder
				->andWhere('d.categorydonnees IN (:categories)')
				->setParameter('categories', $categories);
		}
		if (!empty($from) && !empty($to)) {
			$builder
				->andWhere('d.createdAt BETWEEN :from AND :to OR d.updateAt BETWEEN :from AND :to')
				->setParameter('from', $from)
				->setParameter('to', $to);
		}
		elseif (!empty($from)) {
			$builder
				->andWhere('d.createdAt >= :from OR d.updateAt >= :from')
				->setParameter('from', $from);
		}
		elseif (!empty($to)) {
			$builder
				->andWhere('d.createdAt <= :to OR d.updateAt <= :to')
				->setParameter('to', $to);
		}
		return $builder->getQuery()->getResult();
	}
}
