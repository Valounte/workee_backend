<?php

namespace App\Infrastructure\Feedback\Repository;

use Doctrine\ORM\ORMException;
use App\Core\Components\Team\Entity\Team;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use App\Core\Components\Feedback\Entity\DailyFeedbackTeamPreferences;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Core\Components\Feedback\Repository\DailyFeedbackTeamPreferencesRepositoryInterface;

/**
 * @method DailyFeedbackTeamPreferences|null find($id, $lockMode = null, $lockVersion = null)
 * @method DailyFeedbackTeamPreferences|null findOneBy(array $criteria, array $orderBy = null)
 * @method DailyFeedbackTeamPreferences[]    findAll()
 * @method DailyFeedbackTeamPreferences[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DailyFeedbackTeamPreferencesRepository extends ServiceEntityRepository implements DailyFeedbackTeamPreferencesRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DailyFeedbackTeamPreferences::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(DailyFeedbackTeamPreferences $entity, bool $flush = true): void
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
    public function remove(DailyFeedbackTeamPreferences $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return DailyFeedbackTeamPreferences[] Returns an array of DailyFeedbackTeamPreferences objects
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

    public function findLastDailyFeedbackTeamPreferencesByUser($user): ?DailyFeedbackTeamPreferences
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.created_at', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByTeam(Team $team): ?DailyFeedbackTeamPreferences
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.team = :team')
            ->setParameter('team', $team)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findOneById($id): ?DailyFeedbackTeamPreferences
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    public function findPreferencesInNextMinute(): array
    {
        $now = date("H:i");

        return $this->createQueryBuilder('c')
            ->andWhere('c.sendingTime = :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult()
        ;
    }
}
