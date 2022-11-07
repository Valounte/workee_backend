<?php

namespace App\Infrastructure\EnvironmentMetrics\Repository;

use Doctrine\ORM\ORMException;
use App\Core\Components\User\Entity\User;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Core\Components\Notification\Entity\Enum\NotificationAlertLevelEnum;
use App\Core\Components\EnvironmentMetrics\Entity\EnvironmentMetricsPreferences;
use App\Client\ViewModel\EnvironmentMetrics\EnvironmentMetricsPreferencesViewModel;
use App\Core\Components\EnvironmentMetrics\Repository\EnvironmentMetricsPreferencesRepositoryInterface;

/**
 * @method EnvironmentMetricsPreferences|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnvironmentMetricsPreferences|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnvironmentMetricsPreferences[]    findAll()
 * @method EnvironmentMetricsPreferences[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnvironmentMetricsPreferencesRepository extends ServiceEntityRepository implements EnvironmentMetricsPreferencesRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnvironmentMetricsPreferences::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(EnvironmentMetricsPreferences $entity, bool $flush = true): void
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
    public function remove(EnvironmentMetricsPreferences $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return EnvironmentMetricsPreferences[] Returns an array of EnvironmentMetricsPreferences objects
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

    public function findOneById($id): ?EnvironmentMetricsPreferences
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getAllEnvironmentMetricsPreferences(User $user): array
    {
        $rawResult = $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;

        $notiticationsPreferencesViewModels = [];
        foreach ($rawResult as $notificationPreference) {
            $notiticationsPreferencesViewModels[] = new EnvironmentMetricsPreferencesViewModel(
                $notificationPreference->getId(),
                $notificationPreference->getMetricType(),
                $notificationPreference->getIsDesactivated(),
            );
        }

        return $notiticationsPreferencesViewModels;
    }

    public function getOneByUserAndMetricType(User $user, string $metricType): EnvironmentMetricsPreferences
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->andWhere('c.metricType = :metricType')
            ->setParameter('user', $user)
            ->setParameter('metricType', $metricType)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
