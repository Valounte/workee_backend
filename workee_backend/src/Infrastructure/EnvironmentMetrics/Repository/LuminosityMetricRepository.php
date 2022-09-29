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
        $date = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $date->sub(new \DateInterval('PT10M'));

        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->andWhere('c.created_at > :date')
            ->setParameter('date', $date)
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


    public function findLuminosityHistoric($user): ?array
    {
        $historic = [];

        for ($i = 0; $i <= 5; $i++) {
            $from = date("Y-m-d H:i:s", strtotime("-" . $i . "hours"));
            $to = date("Y-m-d H:i:s", strtotime("-" . ($i + 1) . "hours"));

            $value =  $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->andWhere('c.created_at BETWEEN :to AND :from')
            ->setParameter('user', $user)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('c.created_at', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

            if ($value != null) {
                $historic[] = $value;
            }
        }

        return $historic;
    }
}
