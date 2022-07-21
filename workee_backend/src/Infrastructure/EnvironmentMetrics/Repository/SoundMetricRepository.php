<?php

namespace App\Infrastructure\EnvironmentMetrics\Repository;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use App\Core\Components\EnvironmentMetrics\Entity\SoundMetric;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Core\Components\EnvironmentMetrics\Repository\SoundMetricRepositoryInterface;

/**
 * @method SoundMetric|null find($id, $lockMode = null, $lockVersion = null)
 * @method SoundMetric|null findOneBy(array $criteria, array $orderBy = null)
 * @method SoundMetric[]    findAll()
 * @method SoundMetric[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SoundMetricRepository extends ServiceEntityRepository implements SoundMetricRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SoundMetric::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(SoundMetric $entity, bool $flush = true): void
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
    public function remove(SoundMetric $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return SoundMetric[] Returns an array of SoundMetric objects
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

    public function findLastSoundMetricByUser($user): ?SoundMetric
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.created_at', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findOneById($id): ?SoundMetric
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
