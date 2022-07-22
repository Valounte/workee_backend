<?php

namespace App\Infrastructure\User\Repository;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use App\Core\Components\User\Entity\UserTeam;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class UserTeamRepository extends ServiceEntityRepository implements UserTeamRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserTeam::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(UserTeam $entity, bool $flush = true): void
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
    public function remove(UserTeam $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return UserTeam[] Returns an array of UserTeam objects
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

    public function findOneById($id): ?UserTeam
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //find teams by user
    public function findTeamsByUser($user): ?array
    {
        return $this->createQueryBuilder('c')
            ->select('t')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->leftJoin(
                'App\Core\Components\Team\Entity\Team',
                't',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'c.team = t.id'
            )
            ->getQuery()
            ->getResult()
        ;
    }

    //find users by team and Company
    public function findUsersByTeamId($team): ?array
    {
        return $this->createQueryBuilder('c')
            ->select('u')
            ->andWhere('c.team = :team')
            ->setParameter('team', $team)
            ->leftJoin(
                'App\Core\Components\User\Entity\User',
                'u',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'c.user = u.id'
            )
            ->getQuery()
            ->getResult()
        ;
    }
}
