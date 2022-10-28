<?php

namespace App\Infrastructure\Notification\Repository;

use App\Client\ViewModel\Notification\NotificationPreferencesViewModel;
use App\Core\Components\Notification\Entity\Enum\NotificationAlertLevelEnum;
use Doctrine\ORM\ORMException;
use App\Core\Components\User\Entity\User;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use App\Core\Components\Notification\Entity\NotificationPreferences;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Core\Components\Notification\Repository\NotificationPreferencesRepositoryInterface;

/**
 * @method NotificationPreferences|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationPreferences|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationPreferences[]    findAll()
 * @method NotificationPreferences[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationPreferencesRepository extends ServiceEntityRepository implements NotificationPreferencesRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationPreferences::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(NotificationPreferences $entity, bool $flush = true): void
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
    public function remove(NotificationPreferences $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return NotificationPreferences[] Returns an array of NotificationPreferences objects
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

    public function findOneById($id): ?NotificationPreferences
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getAllNotificationsPreferences(User $user): array
    {
        $rawResult = $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;

        $notiticationsPreferencesViewModels = [];
        foreach ($rawResult as $notificationPreference) {
            $notiticationsPreferencesViewModels[] = new NotificationPreferencesViewModel(
                $notificationPreference->getId(),
                $notificationPreference->getAlertLevel(),
                $notificationPreference->getIsMute(),
            );
        }

        return $notiticationsPreferencesViewModels;
    }

    public function getOneByUserAndAlertLevel(User $user, NotificationAlertLevelEnum $alertLevel): NotificationPreferences
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->andWhere('c.alertLevel = :alertLevel')
            ->setParameter('user', $user)
            ->setParameter('alertLevel', $alertLevel)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
