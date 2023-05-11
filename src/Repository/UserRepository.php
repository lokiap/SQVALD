<?php

namespace App\Repository;

use App\Classe\SearchMembre;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

	public function findAdmins() {
		$rsm = new ResultSetMappingBuilder($this->_em);
		$rsm->addRootEntityFromClassMetadata(User::class, 'u');

		return $this->_em->createNativeQuery('select * from member u where roles::text LIKE \'%"ROLE_ADMIN"%\'', $rsm)
			->getResult();
	}

	public function findByPartner($partners) {
		return $this->createQueryBuilder('u')
			->andWhere('u.isValide = true')
			->andWhere('u.isVerified = true')
			->andWhere('u.partner IN (:partners)')
			->setParameter('partners', $partners)
			->getQuery()
			->getResult();
	}

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * requête qui permet de recupérer les membres en fonctions de la recherche de l'utilisateur
     * @return User[]
     */
    public function findwithSearchMembre(SearchMembre $search)
    {
        //creation d'une requete
        $query = $this
            ->createQueryBuilder('u')
            ->select('u');

        if(!empty($search->string)){
            $query = $query
                ->andWhere('u.firstname Like :string or u.lastname Like :string')

                ->setParameter('string', "%{$search->string}%");

        }

        return $query->getQuery()->getResult();
    }
}
