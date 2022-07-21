<?php

namespace App\Infrastructure\Job\Repository;

use Doctrine\ORM\ORMException;
use App\Core\Components\Job\Entity\Job;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use App\Core\Components\Company\Entity\Company;
use App\Core\Components\Job\Repository\JobRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository implements JobRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Job $entity, bool $flush = true): void
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
    public function remove(Job $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Job[] Returns an array of Job objects
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

    public function findOneById($id): ?Job
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //find by company
    public function findByCompany(Company $company): ?array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult()
        ;
    }
}
