<?php

namespace App\Infrastructure\EnvironmentMetrics\Repository;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use App\Core\Components\EnvironmentMetrics\Entity\HumidityMetric;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Core\Components\EnvironmentMetrics\Repository\HumidityMetricRepositoryInterface;

/**
 * @method HumidityMetric|null find($id, $lockMode = null, $lockVersion = null)
 * @method HumidityMetric|null findOneBy(array $criteria, array $orderBy = null)
 * @method HumidityMetric[]    findAll()
 * @method HumidityMetric[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HumidityMetricRepository extends ServiceEntityRepository implements HumidityMetricRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HumidityMetric::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(HumidityMetric $entity, bool $flush = true): void
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
    public function remove(HumidityMetric $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return HumidityMetric[] Returns an array of HumidityMetric objects
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

    public function findLastHumidityMetricByUser($user): ?HumidityMetric
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.created_at', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findOneById($id): ?HumidityMetric
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
