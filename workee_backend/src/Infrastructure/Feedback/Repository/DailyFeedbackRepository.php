<?php

namespace App\Infrastructure\Feedback\Repository;

use Doctrine\ORM\ORMException;
use App\Core\Components\Team\Entity\Team;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use App\Core\Components\Feedback\Entity\DailyFeedback;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Core\Components\Feedback\Repository\DailyFeedbackRepositoryInterface;

/**
 * @method DailyFeedback|null find($id, $lockMode = null, $lockVersion = null)
 * @method DailyFeedback|null findOneBy(array $criteria, array $orderBy = null)
 * @method DailyFeedback[]    findAll()
 * @method DailyFeedback[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DailyFeedbackRepository extends ServiceEntityRepository implements DailyFeedbackRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DailyFeedback::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(DailyFeedback $entity, bool $flush = true): void
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
    public function remove(DailyFeedback $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return DailyFeedback[] Returns an array of DailyFeedback objects
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

    public function findLastDailyFeedbackByUser($user): ?DailyFeedback
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.created_at', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findOneById($id): ?DailyFeedback
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findLastWeekDailyFeedbackByTeam(Team $team): array
    {
        $timeLimit = new \DateTime();
        $timeLimit->modify('-7 days');

        return $this->createQueryBuilder('c')
            ->andWhere('c.team = :team')
            ->andWhere('c.created_at > :timeLimit')
            ->setParameter('team', $team)
            ->setParameter('timeLimit', $timeLimit)
            ->getQuery()
            ->getResult();
    }
}
