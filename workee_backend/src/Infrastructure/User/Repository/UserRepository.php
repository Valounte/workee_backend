<?php

namespace App\Infrastructure\User\Repository;

use App\Core\Components\Company\Entity\Company;
use Doctrine\ORM\ORMException;
use App\Core\Components\User\Entity\User;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(User $entity, bool $flush = true): void
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
    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
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

    public function findByTeamId(int $team): ?array
    {
        $query = $this->createQueryBuilder('u')
        ->andWhere('u.team = :team')
        ->setParameter('team', $team)
        ->getQuery();

        return $query->getArrayResult();
    }

    public function findUserByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findUserById(int $id): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //find all users by company
    public function findByCompany(Company $company): ?array
    {
        return $this->createQueryBuilder('u')
        ->andWhere('u.company = :company')
        ->setParameter('company', $company)
        ->getQuery()
        ->getResult();
    }
}
