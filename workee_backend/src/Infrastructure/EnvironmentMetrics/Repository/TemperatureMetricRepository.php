<?php

namespace App\Infrastructure\EnvironmentMetrics\Repository;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use App\Core\Components\EnvironmentMetrics\Entity\TemperatureMetric;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Core\Components\EnvironmentMetrics\Repository\TemperatureMetricRepositoryInterface;

/**
 * @method TemperatureMetric|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemperatureMetric|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemperatureMetric[]    findAll()
 * @method TemperatureMetric[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemperatureMetricRepository extends ServiceEntityRepository implements TemperatureMetricRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemperatureMetric::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(TemperatureMetric $entity, bool $flush = true): void
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
    public function remove(TemperatureMetric $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return TemperatureMetric[] Returns an array of TemperatureMetric objects
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


    public function findOneById($id): ?TemperatureMetric
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
