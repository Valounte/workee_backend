<?php

namespace App\Infrastructure\EnvironmentMetrics\Repository;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use App\Core\Components\EnvironmentMetrics\Entity\LuminosityMetric;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Core\Components\EnvironmentMetrics\Repository\LuminosityMetricRepositoryInterface;

/**
 * @method LuminosityMetric|null find($id, $lockMode = null, $lockVersion = null)
 * @method LuminosityMetric|null findOneBy(array $criteria, array $orderBy = null)
 * @method LuminosityMetric[]    findAll()
 * @method LuminosityMetric[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LuminosityMetricRepository extends ServiceEntityRepository implements LuminosityMetricRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LuminosityMetric::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(LuminosityMetric $entity, bool $flush = true): void
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
    public function remove(LuminosityMetric $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return LuminosityMetric[] Returns an array of LuminosityMetric objects
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

    public function findLastLuminosityMetricByUser($user): ?LuminosityMetric
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.created_at', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findOneById($id): ?LuminosityMetric
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}