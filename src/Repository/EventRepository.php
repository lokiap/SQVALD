<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use function PHPUnit\Framework\isEmpty;

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

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Event $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Event $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

	public function findByYear($year) {
		$rangeBegin = \DateTime::createFromFormat('Y-m-d', $year.'-01-01');
		$rangeEnd = \DateTime::createFromFormat('Y-m-d', $year.'-12-31');
		return $this->createQueryBuilder('e')
			->andWhere('e.dateBegin BETWEEN :begin AND :end')
			->andWhere('e.isActive = true')
			->setParameter('begin', $rangeBegin)
			->setParameter('end', $rangeEnd)
			->orderBy('e.dateBegin', 'ASC')
			->getQuery()
			->getResult();
	}

	public function findSinceDate($date, $limit = 1) {
		return $this->createQueryBuilder('e')
			->andWhere('e.isActive = true')
			->andWhere('e.dateBegin > :date')
			->setParameter('date', $date)
			->orderBy('e.dateBegin', 'ASC')
			->setMaxResults($limit)
			->getQuery()
			->getResult();
	}

	public function findWithSearch($keyword, $place, $categories, $isEndBefore, $date) {
		$builder = $this
			->createQueryBuilder('e')
			->andWhere('e.isActive = true')
			->orderBy('e.createdAt', 'DESC');
		if (!empty($keyword)) {
			$builder
				->andWhere('e.title LIKE :keyword')
				->setParameter('keyword', '%'.$keyword.'%');
		}
		if (!empty($place)) {
			$builder
				->andWhere('e.place LIKE :place')
				->setParameter('place', '%'.$place.'%');
		}
		if (!empty($categories)) {
			$builder
				->andWhere('e.category IN (:categories)')
				->setParameter('categories', $categories);
		}
		if (!(empty($isEndBefore) || empty($date))) {
			$builder->setParameter('date', $date);
			if ($isEndBefore == 1) {
				$builder
					->andWhere('e.dateEnd <= :date');
			}
			elseif ($isEndBefore == 2) {
				$builder
					->andWhere('e.dateBegin >= :date');
			}
		}
		return $builder->getQuery()->getResult();
	}
}
