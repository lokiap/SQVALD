<?php

namespace App\Repository;



use App\Classe\SearchNews;
use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function findNewsByCriteria($criteria)
    {
        $query = $this->_em->createQuery('SELECT n FROM App\Entity\News n JOIN n.categorynews c WHERE c.name = :criteria AND n.isActive=1
        ORDER BY n.createdAt DESC ');
        $query->setParameter('criteria', $criteria);
        //$query->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true);
        $tab = $query->getResult();

        return $tab;
    }

    public function findwithNews($criteria)
    {
        $query = $this->_em->createQuery('SELECT n FROM App\Entity\News n JOIN n.categorynews c WHERE c.name = :criteria AND n.isActive=1
        ORDER BY n.createdAt DESC ');
        $query->setParameter('criteria', $criteria);
        $query->setMaxResults(3);
        //$query->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true);
        $tab = $query->getResult();

        return $tab;
    }

    public function findwithEventsAndSeminars($criteria)
    {
        $query = $this->_em->createQuery('SELECT n FROM App\Entity\News n JOIN n.categorynews c WHERE c.name = :criteria AND n.isActive=1
        ORDER BY n.createdAt DESC ');
        $query->setParameter('criteria', $criteria);
        $query->setMaxResults(1);
        //$query->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true);
        $tab = $query->getResult();

        return $tab;
    }

    public function findwithSearchNews(SearchNews $search, $criteria) {
        $searchString = empty($search->string) ? '' : $search->string;
       // die(var_dump(($search)));
        $startCreatedAt = empty($search->startCreatedAt) ? '' : $search->startCreatedAt->format('Y-m-d');
        $searchEndDate = empty($search->endCreatedAt) ? '' : $search->endCreatedAt->format('Y-m-d');

        //die(var_dump($search->string));
        $okQuery = false;

        $tab = array();

        if(!empty($searchString) && !empty($startCreatedAt) && !empty($searchEndDate)) {
            $okQuery = true;
            $query = $this->_em->createQuery('SELECT n FROM App\Entity\News n 
            JOIN n.categorynews c  WHERE c.name = :criteria AND n.isActive=1 AND 
            (n.title LIKE :searchString OR  n.startCreatedAt >= :startCreatedAt AND n.endCreatedAt <= :searchEndDate)');
            //die(var_dump($query));
            $query->setParameter('searchString', '%' . $searchString . '%');
            $query->setParameter('startCreatedAt', $startCreatedAt);
            $query->setParameter('searchEndDate', $searchEndDate);
        } elseif(!empty($searchString)) {
            //die(var_dump($search));
            $okQuery = true;
            $query = $this->_em->createQuery('SELECT n FROM App\Entity\News n 
            JOIN n.categorynews c  WHERE c.name = :criteria AND n.isActive=1 AND 
            n.title LIKE :searchString ORDER BY n.createdAt DESC');
            $query->setParameter('searchString', '%' . $searchString . '%');
        } elseif(!empty($startCreatedAt) && !empty($searchEndDate)) {
            $okQuery = true;
            $query = $this->_em->createQuery('SELECT n FROM App\Entity\News n 
            JOIN n.categorynews c  WHERE c.name = :criteria AND n.isActive=1 AND 
            (n.startCreatedAt >= :startCreatedAt AND n.endCreatedAt <= :searchEndDate)
            ORDER BY n.createdAt DESC');
            $query->setParameter('startCreatedAt', $startCreatedAt);
            $query->setParameter('searchEndDate', $searchEndDate);
        }

        if($okQuery) {
            $query->setParameter('criteria', $criteria);
            //$query->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true);
            $tab = $query->getResult();
        }

        return $tab;
    }

	public function findWithSearch($keyword, $from, $to) {
		$builder = $this
			->createQueryBuilder('n')
			->andWhere('n.isActive = true')
			->orderBy('n.createdAt', 'DESC');
		if (!empty($keyword)) {
			$builder
				->andWhere('n.title LIKE :keyword')
				->setParameter('keyword', '%'.$keyword.'%');
		}
		if (!empty($from) && !empty($to)) {
			$builder
				->andWhere('n.createdAt BETWEEN :from AND :to')
				->setParameter('from', $from)
				->setParameter('to', $to);
		}
		elseif (!empty($from)) {
			$builder
				->andWhere('n.createdAt >= :from')
				->setParameter('from', $from);
		}
		elseif (!empty($to)) {
			$builder
				->andWhere('n.createdAt <= :to')
				->setParameter('to', $to);
		}
//		dd($from, $to, empty($form) && empty($to), empty($form), empty($to), isset($from), isset($to));
		return $builder->getQuery()->getResult();
	}

}
